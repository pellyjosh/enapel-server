<?php

namespace App\Services\DisasterRecovery;

use App\Models\BackupRun;
use App\Models\ReplicationCheckpoint;
use App\Models\ReplicationNode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class ReplicationService
{
    public function __construct(
        protected DisasterRecoveryService $recovery,
        protected NodeStateService $nodeState,
        protected SignatureService $signatureService
    ) {
    }

    public function generatePairingToken(): string
    {
        $token = Str::upper(Str::random(48));
        $settings = $this->recovery->settings();
        $meta = $settings->meta ?? [];
        $meta['pairing_token_generated_at'] = now()->toIso8601String();

        $settings->update([
            'standby_pairing_token' => $token,
            'meta' => $meta,
        ]);

        return $token;
    }

    public function pairWithPrimary(string $primaryUrl, string $token): array
    {
        $primaryUrl = rtrim($primaryUrl, '/');
        $state = $this->nodeState->get();
        $settings = $this->recovery->settings();
        $body = [
            'pairing_token' => $token,
            'node_uuid' => $state['node_uuid'],
            'node_name' => $state['node_name'] ?? $settings->node_name,
            'hostname' => gethostname() ?: null,
            'base_url' => $this->localBaseUrl($settings),
        ];

        $response = Http::timeout(15)
            ->acceptJson()
            ->post($primaryUrl.'/api/v1/dr/pairing', $body);

        if (! $response->successful()) {
            throw new RuntimeException($response->json('message') ?: 'Pairing with the primary node failed.');
        }

        $payload = $response->json();

        $this->nodeState->update([
            'role' => 'standby',
            'primary_url' => $primaryUrl,
            'shared_secret' => $payload['shared_secret'],
            'pairing' => [
                'token' => null,
                'paired_at' => now()->toIso8601String(),
                'primary_node_uuid' => $payload['primary_node_uuid'] ?? null,
            ],
            'replication_paused' => false,
        ]);

        $settings->update([
            'node_role' => 'standby',
            'standby_enabled' => true,
            'standby_primary_url' => $primaryUrl,
        ]);

        return $payload;
    }

    public function registerPairing(array $payload, Request $request): array
    {
        $this->assertPrivateRequest($request);

        $settings = $this->recovery->settings();
        $token = $payload['pairing_token'] ?? null;

        if (! filled($token) || ! hash_equals((string) $settings->standby_pairing_token, (string) $token)) {
            throw new RuntimeException('Pairing token is invalid or has already been used.');
        }

        $node = ReplicationNode::query()->updateOrCreate(
            ['node_uuid' => $payload['node_uuid']],
            [
                'name' => $payload['node_name'] ?? 'Standby Node',
                'role' => 'standby',
                'hostname' => $payload['hostname'] ?? null,
                'base_url' => $payload['base_url'] ?? null,
                'shared_secret' => Str::random(64),
                'pair_token_hash' => $this->signatureService->hashPairToken((string) $token),
                'status' => 'healthy',
                'paired_at' => now(),
                'last_seen_at' => now(),
            ]
        );

        $settings->update([
            'standby_pairing_token' => null,
            'standby_enabled' => true,
            'last_standby_seen_at' => now(),
        ]);

        return [
            'node_uuid' => $payload['node_uuid'],
            'primary_node_uuid' => $this->nodeState->get()['node_uuid'],
            'shared_secret' => $node->shared_secret,
            'service_hostname' => $settings->service_hostname,
            'max_standby_lag_seconds' => config('disaster-recovery.max_standby_lag_seconds', 60),
        ];
    }

    public function validateSignedRequest(Request $request): ReplicationNode
    {
        $this->assertPrivateRequest($request);

        $nodeUuid = (string) $request->header('X-Dr-Node', '');

        if ($nodeUuid === '') {
            throw new RuntimeException('Missing DR node identity.');
        }

        $node = ReplicationNode::query()->where('node_uuid', $nodeUuid)->first();

        if (! $node || blank($node->shared_secret)) {
            throw new RuntimeException('Unknown standby node.');
        }

        $this->signatureService->validate($request, $node->shared_secret);

        return $node;
    }

    public function fetchPrimaryStatus(): array
    {
        $state = $this->nodeState->get();
        $primaryUrl = rtrim((string) ($state['primary_url'] ?? ''), '/');
        $sharedSecret = (string) ($state['shared_secret'] ?? '');
        $nodeUuid = (string) ($state['node_uuid'] ?? '');

        if ($primaryUrl === '' || $sharedSecret === '' || $nodeUuid === '') {
            throw new RuntimeException('Standby node is not paired with a primary.');
        }

        $after = ReplicationCheckpoint::query()
            ->where('status', 'applied')
            ->latest('applied_at')
            ->value('applied_at');

        $query = ['after' => optional($after)->toIso8601String()];
        $path = '/api/v1/dr/status?'.http_build_query($query);

        $response = Http::timeout(15)
            ->acceptJson()
            ->withHeaders($this->signatureService->makeHeaders($sharedSecret, $nodeUuid, 'GET', '/api/v1/dr/status', ''))
            ->get($primaryUrl.'/api/v1/dr/status', $query);

        if (! $response->successful()) {
            throw new RuntimeException($response->json('message') ?: 'Unable to fetch primary replication status.');
        }

        return $response->json();
    }

    public function pullAvailableBundles(int $iterations = 1, int $intervalSeconds = 0): array
    {
        if ($this->nodeState->role() !== 'standby') {
            return [
                'status' => 'skipped',
                'message' => 'This node is not in standby mode.',
                'applied' => [],
            ];
        }

        $applied = [];

        if (! ReplicationCheckpoint::query()->where('status', 'applied')->exists()) {
            $seedResult = $this->seedFromNasIfAvailable();

            if ($seedResult !== null) {
                $applied[] = $seedResult;
            }
        }

        for ($iteration = 0; $iteration < $iterations; $iteration++) {
            $status = $this->fetchPrimaryStatus();

            foreach (Arr::wrap($status['bundles'] ?? []) as $bundle) {
                $applied[] = $this->downloadAndApplyBundle($bundle);
            }

            $this->sendHeartbeat();

            if (($iteration + 1) < $iterations && $intervalSeconds > 0) {
                sleep($intervalSeconds);
            }
        }

        return [
            'status' => 'ok',
            'applied' => $applied,
        ];
    }

    public function sendHeartbeat(): void
    {
        $state = $this->nodeState->get();
        $primaryUrl = rtrim((string) ($state['primary_url'] ?? ''), '/');
        $sharedSecret = (string) ($state['shared_secret'] ?? '');

        if ($primaryUrl === '' || $sharedSecret === '') {
            return;
        }

        $payload = [
            'node_uuid' => $state['node_uuid'],
            'node_name' => $state['node_name'],
            'last_sync_at' => $state['last_sync_at'],
            'last_sync_bundle_uuid' => $state['last_sync_bundle_uuid'],
        ];

        Http::timeout(10)
            ->acceptJson()
            ->withHeaders($this->signatureService->makeHeaders($sharedSecret, $state['node_uuid'], 'POST', '/api/v1/dr/heartbeat', $payload))
            ->post($primaryUrl.'/api/v1/dr/heartbeat', $payload);
    }

    public function recordHeartbeat(Request $request): array
    {
        $node = $this->validateSignedRequest($request);

        $node->update([
            'status' => 'healthy',
            'last_seen_at' => now(),
            'meta' => array_merge($node->meta ?? [], [
                'heartbeat' => $request->all(),
            ]),
        ]);

        $settings = $this->recovery->settings();
        $settings->update([
            'last_standby_seen_at' => now(),
        ]);

        return ['ok' => true];
    }

    protected function seedFromNasIfAvailable(): ?array
    {
        $settings = $this->recovery->settings();

        if (blank($settings->nas_path) || ! File::isDirectory($settings->nas_path)) {
            return null;
        }

        $bundles = $this->recovery->availableNasBundles($settings->nas_path);

        if ($bundles === []) {
            return null;
        }

        $result = $this->recovery->restoreFromNas($settings->nas_path, $this->recovery->currentPassphrase());
        $last = $result['last_applied']['metadata'] ?? [];

        $checkpoint = ReplicationCheckpoint::create([
            'checkpoint_uuid' => (string) Str::uuid(),
            'type' => 'nas-seed',
            'status' => 'applied',
            'source_bundle_uuid' => $last['bundle_uuid'] ?? null,
            'source_bundle_path' => Arr::last($result['chain'])['path'] ?? null,
            'sync_age_seconds' => 0,
            'applied_at' => now(),
            'manifest' => $result['last_applied']['manifest'] ?? null,
            'meta' => [
                'source' => 'nas',
                'chain' => array_map(fn (array $bundle) => $bundle['header'], $result['chain']),
            ],
        ]);

        $this->nodeState->update([
            'last_sync_at' => now()->toIso8601String(),
            'last_sync_bundle_uuid' => $checkpoint->source_bundle_uuid,
        ]);

        return [
            'source' => 'nas',
            'bundle_uuid' => $checkpoint->source_bundle_uuid,
        ];
    }

    protected function downloadAndApplyBundle(array $bundle): array
    {
        $state = $this->nodeState->get();
        $primaryUrl = rtrim((string) $state['primary_url'], '/');
        $path = '/api/v1/dr/bundles/'.$bundle['bundle_uuid'];

        $response = Http::timeout(120)
            ->withHeaders($this->signatureService->makeHeaders($state['shared_secret'], $state['node_uuid'], 'GET', $path, ''))
            ->get($primaryUrl.$path);

        if (! $response->successful()) {
            throw new RuntimeException($response->json('message') ?: 'Failed to download standby replication bundle.');
        }

        $workspace = storage_path('app/private/dr/tmp/downloads');
        File::ensureDirectoryExists($workspace);

        $downloadPath = $workspace.DIRECTORY_SEPARATOR.$bundle['bundle_uuid'].'.drb';
        File::put($downloadPath, $response->body());

        $restore = $this->recovery->applyBundle($downloadPath, $this->recovery->currentPassphrase(), [
            'restore_env' => true,
            'run_migrations' => false,
        ]);

        $node = ReplicationNode::query()->firstOrCreate(
            ['node_uuid' => $state['node_uuid']],
            [
                'name' => $state['node_name'] ?? 'Standby Node',
                'role' => 'standby',
                'status' => 'healthy',
            ]
        );

        $backupRun = BackupRun::query()->where('bundle_uuid', $bundle['bundle_uuid'])->first();

        ReplicationCheckpoint::create([
            'checkpoint_uuid' => (string) Str::uuid(),
            'replication_node_id' => $node->id,
            'backup_run_id' => $backupRun?->id,
            'type' => $bundle['type'] ?? 'snapshot',
            'status' => 'applied',
            'source_bundle_uuid' => $bundle['bundle_uuid'],
            'source_bundle_path' => $downloadPath,
            'sync_age_seconds' => filled($bundle['completed_at'] ?? null) ? now()->diffInSeconds(Carbon::parse($bundle['completed_at'])) : null,
            'pulled_at' => now(),
            'applied_at' => now(),
            'manifest' => $restore['manifest'],
            'meta' => [
                'source' => 'primary',
                'header' => $bundle,
            ],
        ]);

        $node->update([
            'status' => 'healthy',
            'sync_lag_seconds' => filled($bundle['completed_at'] ?? null) ? now()->diffInSeconds(Carbon::parse($bundle['completed_at'])) : null,
            'last_pull_at' => now(),
            'last_backup_at' => filled($bundle['completed_at'] ?? null) ? Carbon::parse($bundle['completed_at']) : now(),
            'last_backup_run_id' => $backupRun?->id,
        ]);

        $this->nodeState->update([
            'last_sync_at' => now()->toIso8601String(),
            'last_sync_bundle_uuid' => $bundle['bundle_uuid'],
        ]);

        return [
            'bundle_uuid' => $bundle['bundle_uuid'],
            'type' => $bundle['type'] ?? 'snapshot',
        ];
    }

    protected function assertPrivateRequest(Request $request): void
    {
        if (! config('disaster-recovery.private_ip_only', true)) {
            return;
        }

        if (! $this->isPrivateIp($request->ip())) {
            throw new RuntimeException('DR endpoints only accept requests from private network addresses.');
        }
    }

    protected function isPrivateIp(?string $ip): bool
    {
        if (! $ip) {
            return false;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ipLong = ip2long($ip);

            if ($ipLong === false) {
                return false;
            }

            return ($ipLong >= ip2long('10.0.0.0') && $ipLong <= ip2long('10.255.255.255'))
                || ($ipLong >= ip2long('172.16.0.0') && $ipLong <= ip2long('172.31.255.255'))
                || ($ipLong >= ip2long('192.168.0.0') && $ipLong <= ip2long('192.168.255.255'))
                || ($ipLong >= ip2long('127.0.0.0') && $ipLong <= ip2long('127.255.255.255'));
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $normalized = strtolower($ip);

            return $normalized === '::1'
                || str_starts_with($normalized, 'fc')
                || str_starts_with($normalized, 'fd')
                || str_starts_with($normalized, 'fe80');
        }

        return false;
    }

    protected function localBaseUrl($settings): ?string
    {
        $host = $settings->service_hostname ?: gethostname();
        $port = config('nativephp.server_port') ?: env('NATIVEPHP_SERVER_PORT', 8000);

        return $host ? "http://{$host}:{$port}" : null;
    }
}
