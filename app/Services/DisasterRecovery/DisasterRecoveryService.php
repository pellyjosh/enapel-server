<?php

namespace App\Services\DisasterRecovery;

use App\Models\BackupRun;
use App\Models\DisasterRecoverySetting;
use App\Models\ReplicationCheckpoint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

class DisasterRecoveryService
{
    public function __construct(
        protected BundleCryptoService $crypto,
        protected EnvBundleService $envBundleService,
        protected NodeStateService $nodeState
    ) {
    }

    public function settings(): DisasterRecoverySetting
    {
        return DisasterRecoverySetting::current();
    }

    public function saveSettings(array $attributes): DisasterRecoverySetting
    {
        $settings = $this->settings();
        $persistable = Arr::except($attributes, ['passphrase', 'passphrase_confirmation']);

        if (filled($attributes['passphrase'] ?? null)) {
            $persistable['encrypted_passphrase'] = $attributes['passphrase'];
            $persistable['dr_passphrase_hash'] = Hash::make($attributes['passphrase']);
        }

        $settings->fill($persistable);
        $settings->save();

        $nodeUpdates = [];

        if (array_key_exists('node_role', $persistable)) {
            $nodeUpdates['role'] = $persistable['node_role'];
        }

        if (array_key_exists('node_name', $persistable)) {
            $nodeUpdates['node_name'] = $persistable['node_name'];
        }

        if ($nodeUpdates !== []) {
            $this->nodeState->update($nodeUpdates);
        }

        if (filled($settings->nas_path)) {
            File::ensureDirectoryExists($settings->nas_path);
        }

        return $settings->fresh();
    }

    public function currentPassphrase(?string $override = null): string
    {
        if (filled($override)) {
            return $override;
        }

        $settings = $this->settings();

        if (blank($settings->encrypted_passphrase)) {
            throw new RuntimeException('Disaster recovery passphrase has not been configured.');
        }

        return $settings->encrypted_passphrase;
    }

    public function runSnapshot(string $type = 'snapshot', bool $full = false, ?string $passphrase = null): BackupRun
    {
        $settings = $this->settings();

        if (blank($settings->nas_path)) {
            throw new RuntimeException('NAS backup path is not configured.');
        }

        $passphrase = $this->currentPassphrase($passphrase);
        File::ensureDirectoryExists($settings->nas_path);

        $run = BackupRun::create([
            'bundle_uuid' => (string) Str::uuid(),
            'type' => $type,
            'status' => BackupRun::STATUS_RUNNING,
            'storage_target' => 'nas',
            'bundle_name' => $this->bundleFileName($type),
            'started_at' => now(),
            'meta' => [],
        ]);

        $workspace = $this->tempDirectory('backup-'.$run->bundle_uuid);

        try {
            $databaseSnapshotPath = $workspace.DIRECTORY_SEPARATOR.'database.sqlite';
            $databaseBytes = $this->createDatabaseSnapshot($databaseSnapshotPath);

            $currentManifest = $this->buildStorageManifest();
            $previousManifest = BackupRun::query()
                ->whereIn('status', [
                    BackupRun::STATUS_COMPLETED,
                    BackupRun::STATUS_MIRRORED,
                    BackupRun::STATUS_RESTORED,
                ])
                ->whereNotNull('manifest')
                ->latest('completed_at')
                ->value('manifest') ?? [];

            $full = $full || blank($previousManifest);
            $included = $full ? $currentManifest : $this->diffIncludedPaths($currentManifest, $previousManifest);
            $deleted = $full ? [] : $this->diffDeletedPaths($currentManifest, $previousManifest);

            $this->stageFiles($workspace, array_keys($included));

            $envBundle = $this->envBundleService->capture($settings);
            $nodeState = $this->nodeState->get();

            $metadata = [
                'bundle_uuid' => $run->bundle_uuid,
                'type' => $type,
                'full' => $full,
                'created_at' => now()->toIso8601String(),
                'node_uuid' => $nodeState['node_uuid'],
                'node_role' => $nodeState['role'],
                'service_hostname' => $settings->service_hostname,
                'manifest' => $currentManifest,
                'included_paths' => array_keys($included),
                'deleted_paths' => array_values($deleted),
            ];

            file_put_contents($workspace.DIRECTORY_SEPARATOR.'manifest.json', json_encode($currentManifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            file_put_contents($workspace.DIRECTORY_SEPARATOR.'metadata.json', json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            file_put_contents($workspace.DIRECTORY_SEPARATOR.'env.json', json_encode($envBundle, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            $archivePath = $workspace.DIRECTORY_SEPARATOR.'bundle.zip';
            $this->createZipArchive($workspace, $archivePath);

            $targetDirectory = $this->buildNasDirectory($settings->nas_path, $type);
            File::ensureDirectoryExists($targetDirectory);

            $destinationPath = $targetDirectory.DIRECTORY_SEPARATOR.$run->bundle_name;
            $header = [
                'bundle_uuid' => $run->bundle_uuid,
                'type' => $type,
                'full' => $full,
                'created_at' => $metadata['created_at'],
                'node_uuid' => $nodeState['node_uuid'],
                'service_hostname' => $settings->service_hostname,
            ];

            $this->crypto->encryptFile($archivePath, $destinationPath, $passphrase, $header);

            $run->forceFill([
                'status' => BackupRun::STATUS_COMPLETED,
                'bundle_path' => $destinationPath,
                'checksum' => hash_file('sha256', $destinationPath),
                'size_bytes' => filesize($destinationPath) ?: null,
                'files_count' => count($included),
                'database_bytes' => $databaseBytes,
                'manifest' => $currentManifest,
                'included_paths' => array_keys($included),
                'deleted_paths' => array_values($deleted),
                'meta' => [
                    'full' => $full,
                    'header' => $header,
                    'env_keys' => array_keys($envBundle),
                ],
                'completed_at' => now(),
            ])->save();

            $settings->forceFill([
                'last_successful_snapshot_at' => $type === 'snapshot' ? now() : $settings->last_successful_snapshot_at,
                'last_successful_full_backup_at' => $full ? now() : $settings->last_successful_full_backup_at,
            ])->save();

            $this->nodeState->update([
                'backup_paths' => array_values(array_unique(array_filter([
                    $destinationPath,
                    ...Arr::wrap($nodeState['backup_paths'] ?? []),
                ]))),
            ]);

            return $run->fresh();
        } catch (\Throwable $e) {
            $run->forceFill([
                'status' => BackupRun::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ])->save();

            throw $e;
        } finally {
            $this->deleteDirectory($workspace);
        }
    }

    public function applyBundle(string $bundlePath, string $passphrase, array $options = []): array
    {
        $workspace = $this->tempDirectory('restore-'.Str::uuid());

        try {
            $archivePath = $workspace.DIRECTORY_SEPARATOR.'bundle.zip';
            $header = $this->crypto->decryptFile($bundlePath, $archivePath, $passphrase);

            $zip = new ZipArchive();
            $opened = $zip->open($archivePath);

            if ($opened !== true) {
                throw new RuntimeException("Unable to open decrypted archive [{$bundlePath}].");
            }

            $zip->extractTo($workspace);
            $zip->close();

            $metadata = json_decode(file_get_contents($workspace.DIRECTORY_SEPARATOR.'metadata.json') ?: '[]', true);
            $manifest = json_decode(file_get_contents($workspace.DIRECTORY_SEPARATOR.'manifest.json') ?: '[]', true);
            $envBundle = json_decode(file_get_contents($workspace.DIRECTORY_SEPARATOR.'env.json') ?: '[]', true);

            if (! is_array($metadata) || ! is_array($manifest) || ! is_array($envBundle)) {
                throw new RuntimeException('Backup bundle metadata is invalid.');
            }

            $databaseTarget = $this->currentDatabasePath();
            File::ensureDirectoryExists(dirname($databaseTarget));

            DB::disconnect();
            File::copy($workspace.DIRECTORY_SEPARATOR.'database.sqlite', $databaseTarget);

            if (($metadata['full'] ?? false) === true) {
                $this->synchronizeFullStorage($manifest);
            }

            $this->restoreFilesFromWorkspace($workspace, $metadata['included_paths'] ?? []);
            $this->deletePaths(Arr::wrap($metadata['deleted_paths'] ?? []));

            if (($options['restore_env'] ?? true) === true) {
                $this->envBundleService->restore($envBundle);
            }

            if (($options['run_migrations'] ?? false) === true) {
                Artisan::call('migrate', ['--force' => true]);
            }

            $this->nodeState->update([
                'last_restored_bundle' => $metadata['bundle_uuid'] ?? $header['bundle_uuid'] ?? null,
            ]);

            BackupRun::query()
                ->where('bundle_uuid', $metadata['bundle_uuid'] ?? $header['bundle_uuid'] ?? null)
                ->update([
                    'status' => BackupRun::STATUS_RESTORED,
                    'restored_at' => now(),
                ]);

            return [
                'header' => $header,
                'metadata' => $metadata,
                'manifest' => $manifest,
            ];
        } finally {
            $this->deleteDirectory($workspace);
        }
    }

    public function restoreFromNas(string $nasPath, string $passphrase, ?string $bundleUuid = null): array
    {
        $chain = $this->resolveRestoreChain($nasPath, $bundleUuid);

        if ($chain === []) {
            throw new RuntimeException('No restoreable backup chain was found in the configured NAS path.');
        }

        $lastApplied = null;

        foreach ($chain as $index => $bundle) {
            $lastApplied = $this->applyBundle($bundle['path'], $passphrase, [
                'restore_env' => true,
                'run_migrations' => false,
            ]);

            if (($index + 1) < count($chain)) {
                DB::reconnect();
            }
        }

        Artisan::call('migrate', ['--force' => true]);

        return [
            'chain' => $chain,
            'last_applied' => $lastApplied,
        ];
    }

    public function promote(?string $passphrase = null): array
    {
        $state = $this->nodeState->get();

        if (($state['role'] ?? 'primary') !== 'standby') {
            return [
                'status' => 'already_primary',
                'message' => 'This node is already primary.',
            ];
        }

        $checkpoint = ReplicationCheckpoint::query()
            ->with(['backupRun', 'replicationNode'])
            ->where('status', 'applied')
            ->latest('applied_at')
            ->first();

        if (! $checkpoint) {
            throw new RuntimeException('No standby checkpoint is available to promote.');
        }

        if (($checkpoint->sync_age_seconds ?? PHP_INT_MAX) > config('disaster-recovery.max_standby_lag_seconds', 60)) {
            throw new RuntimeException('Standby data is too stale to promote safely.');
        }

        $bundlePath = $checkpoint->source_bundle_path ?: $checkpoint->backupRun?->bundle_path;

        if (blank($bundlePath) || ! file_exists($bundlePath)) {
            throw new RuntimeException('Latest replicated bundle is missing from disk.');
        }

        $restore = $this->applyBundle($bundlePath, $this->currentPassphrase($passphrase), [
            'restore_env' => true,
            'run_migrations' => true,
        ]);

        if (! $this->verifyDatabaseIntegrityAtPath($this->currentDatabasePath())) {
            throw new RuntimeException('Database integrity check failed after promotion.');
        }

        if (! $this->allManifestFilesExist($restore['manifest'] ?? [])) {
            throw new RuntimeException('Promotion halted because required storage files are missing.');
        }

        $this->nodeState->update([
            'role' => 'primary',
            'replication_paused' => true,
            'last_promoted_snapshot' => $checkpoint->source_bundle_uuid,
            'role_changed_at' => now()->toIso8601String(),
        ]);

        $settings = $this->settings();
        $settings->forceFill([
            'node_role' => 'primary',
            'last_standby_seen_at' => now(),
        ])->save();

        if ($checkpoint->replicationNode) {
            $checkpoint->replicationNode->update([
                'status' => 'promoted',
                'sync_lag_seconds' => 0,
            ]);
        }

        return [
            'status' => 'promoted',
            'bundle_uuid' => $checkpoint->source_bundle_uuid,
        ];
    }

    public function prune(): array
    {
        $settings = $this->settings();
        $deleted = [];

        $snapshotRuns = BackupRun::query()
            ->where('type', 'snapshot')
            ->where('completed_at', '<', now()->subDays($settings->retention_snapshot_days))
            ->get();

        foreach ($snapshotRuns as $run) {
            $deleted[] = $this->pruneRunFile($run);
        }

        $dailyRuns = BackupRun::query()
            ->where('type', 'daily')
            ->where('status', BackupRun::STATUS_COMPLETED)
            ->latest('completed_at')
            ->skip($settings->retention_daily_backups)
            ->take(PHP_INT_MAX)
            ->get();

        foreach ($dailyRuns as $run) {
            $deleted[] = $this->pruneRunFile($run);
        }

        $monthlyRuns = BackupRun::query()
            ->where('type', 'monthly')
            ->where('status', BackupRun::STATUS_COMPLETED)
            ->latest('completed_at')
            ->skip($settings->retention_monthly_backups)
            ->take(PHP_INT_MAX)
            ->get();

        foreach ($monthlyRuns as $run) {
            $deleted[] = $this->pruneRunFile($run);
        }

        return array_values(array_filter($deleted));
    }

    public function healthWarnings(): array
    {
        $settings = $this->settings();
        $warnings = [];

        if (blank($settings->nas_path)) {
            $warnings[] = 'Shared backup folder is not set yet.';
        } elseif (! File::isDirectory($settings->nas_path)) {
            $warnings[] = 'This computer cannot reach the shared backup folder.';
        }

        if (blank($settings->encrypted_passphrase)) {
            $warnings[] = 'Backup password is missing. Automatic backups cannot run yet.';
        }

        if (($this->nodeState->role() === 'standby') && blank($this->nodeState->primaryUrl())) {
            $warnings[] = 'Backup server mode is on, but the main server address is missing.';
        }

        return $warnings;
    }

    public function availableNasBundles(?string $nasPath = null): array
    {
        $path = $nasPath ?: $this->settings()->nas_path;

        if (blank($path) || ! File::isDirectory($path)) {
            return [];
        }

        $bundles = [];

        foreach (File::allFiles($path) as $file) {
            if ($file->getExtension() !== 'drb') {
                continue;
            }

            try {
                $header = $this->crypto->readHeader($file->getPathname());
                $bundles[] = [
                    'path' => $file->getPathname(),
                    'header' => $header,
                ];
            } catch (\Throwable $e) {
                Log::warning('Skipping unreadable DR bundle.', [
                    'path' => $file->getPathname(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        usort($bundles, fn (array $left, array $right) => strcmp(
            $left['header']['created_at'] ?? '',
            $right['header']['created_at'] ?? ''
        ));

        return array_reverse($bundles);
    }

    protected function createDatabaseSnapshot(string $targetPath): int
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            throw new RuntimeException('Disaster recovery snapshots currently require SQLite as the primary database.');
        }

        if (file_exists($targetPath)) {
            unlink($targetPath);
        }

        $escapedPath = str_replace("'", "''", $targetPath);
        DB::connection()->unprepared("VACUUM INTO '{$escapedPath}'");

        $size = filesize($targetPath);

        if ($size === false) {
            throw new RuntimeException('Unable to determine database snapshot size.');
        }

        return $size;
    }

    protected function buildStorageManifest(): array
    {
        $manifest = [];
        $roots = [
            'public' => storage_path('app/public'),
            'private' => storage_path('app/private'),
        ];

        foreach ($roots as $prefix => $root) {
            if (! File::isDirectory($root)) {
                continue;
            }

            foreach (File::allFiles($root) as $file) {
                $relative = str_replace('\\', '/', $file->getRelativePathname());
                $key = $prefix.'/'.$relative;

                if ($this->shouldExcludePath($key)) {
                    continue;
                }

                $manifest[$key] = [
                    'hash' => hash_file('sha256', $file->getPathname()),
                    'size' => $file->getSize(),
                    'modified_at' => date(DATE_ATOM, $file->getMTime()),
                ];
            }
        }

        ksort($manifest);

        return $manifest;
    }

    protected function diffIncludedPaths(array $current, array $previous): array
    {
        return array_filter($current, function (array $meta, string $path) use ($previous) {
            return ! isset($previous[$path]) || $previous[$path] !== $meta;
        }, ARRAY_FILTER_USE_BOTH);
    }

    protected function diffDeletedPaths(array $current, array $previous): array
    {
        return array_values(array_diff(array_keys($previous), array_keys($current)));
    }

    protected function shouldExcludePath(string $path): bool
    {
        return Str::startsWith($path, [
            'private/logs/',
            'private/framework/cache/',
            'private/framework/sessions/',
            'private/framework/views/',
            'private/framework/testing/',
            'private/dr/tmp/',
            'private/temp/',
            'public/temp/',
        ]);
    }

    protected function stageFiles(string $workspace, array $includedPaths): void
    {
        $targetRoot = $workspace.DIRECTORY_SEPARATOR.'files';
        File::ensureDirectoryExists($targetRoot);

        foreach ($includedPaths as $path) {
            $source = $this->storagePathForManifestKey($path);

            if (! file_exists($source)) {
                continue;
            }

            $destination = $targetRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
            File::ensureDirectoryExists(dirname($destination));
            File::copy($source, $destination);
        }
    }

    protected function createZipArchive(string $workspace, string $archivePath): void
    {
        $zip = new ZipArchive();
        $opened = $zip->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($opened !== true) {
            throw new RuntimeException("Unable to create archive [{$archivePath}].");
        }

        foreach (File::allFiles($workspace) as $file) {
            if ($file->getFilename() === basename($archivePath)) {
                continue;
            }

            $zip->addFile($file->getPathname(), str_replace('\\', '/', $file->getRelativePathname()));
        }

        $zip->close();
    }

    protected function storagePathForManifestKey(string $key): string
    {
        [$scope, $relative] = explode('/', $key, 2);

        return storage_path('app/'.$scope.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative));
    }

    protected function restoreFilesFromWorkspace(string $workspace, array $includedPaths): void
    {
        foreach ($includedPaths as $path) {
            $source = $workspace.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);

            if (! file_exists($source)) {
                continue;
            }

            $destination = $this->storagePathForManifestKey($path);
            File::ensureDirectoryExists(dirname($destination));
            File::copy($source, $destination);
        }
    }

    protected function deletePaths(array $paths): void
    {
        foreach ($paths as $path) {
            $target = $this->storagePathForManifestKey($path);

            if (file_exists($target)) {
                File::delete($target);
            }
        }
    }

    protected function synchronizeFullStorage(array $manifest): void
    {
        foreach ($this->buildStorageManifest() as $path => $meta) {
            if (! isset($manifest[$path])) {
                $target = $this->storagePathForManifestKey($path);
                File::delete($target);
            }
        }
    }

    protected function currentDatabasePath(): string
    {
        $database = DB::connection()->getDatabaseName();

        if (is_string($database) && $database !== ':memory:' && $database !== '') {
            return $database;
        }

        $nativeDatabase = config('nativephp-internal.database_path');

        if (is_string($nativeDatabase) && $nativeDatabase !== '') {
            return $nativeDatabase;
        }

        return database_path('database.sqlite');
    }

    protected function tempDirectory(string $suffix): string
    {
        $path = storage_path('app/private/dr/tmp/'.$suffix);
        File::ensureDirectoryExists($path);

        return $path;
    }

    protected function deleteDirectory(string $path): void
    {
        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
        }
    }

    protected function buildNasDirectory(string $nasPath, string $type): string
    {
        return rtrim($nasPath, DIRECTORY_SEPARATOR)
            .DIRECTORY_SEPARATOR.now()->format('Y')
            .DIRECTORY_SEPARATOR.now()->format('m')
            .DIRECTORY_SEPARATOR.$type;
    }

    protected function bundleFileName(string $type): string
    {
        return now()->format('Ymd_His')."_{$type}_".Str::uuid().'.drb';
    }

    protected function resolveRestoreChain(string $nasPath, ?string $bundleUuid = null): array
    {
        $bundles = array_reverse($this->availableNasBundles($nasPath));

        if ($bundles === []) {
            return [];
        }

        if ($bundleUuid) {
            $targetIndex = null;

            foreach ($bundles as $index => $bundle) {
                if (($bundle['header']['bundle_uuid'] ?? null) === $bundleUuid) {
                    $targetIndex = $index;
                    break;
                }
            }

            if ($targetIndex === null) {
                throw new RuntimeException('Requested backup bundle was not found in the NAS path.');
            }
        } else {
            $targetIndex = count($bundles) - 1;
        }

        $startIndex = $targetIndex;

        while ($startIndex >= 0) {
            if (($bundles[$startIndex]['header']['full'] ?? false) === true) {
                break;
            }

            $startIndex--;
        }

        if ($startIndex < 0) {
            throw new RuntimeException('No full backup was found before the requested restore point.');
        }

        return array_slice($bundles, $startIndex, ($targetIndex - $startIndex) + 1);
    }

    protected function verifyDatabaseIntegrityAtPath(string $databasePath): bool
    {
        if (! file_exists($databasePath)) {
            return false;
        }

        $pdo = new \PDO('sqlite:'.$databasePath);
        $statement = $pdo->query('PRAGMA integrity_check');

        return $statement?->fetchColumn() === 'ok';
    }

    protected function allManifestFilesExist(array $manifest): bool
    {
        foreach (array_keys($manifest) as $path) {
            if (! file_exists($this->storagePathForManifestKey($path))) {
                return false;
            }
        }

        return true;
    }

    protected function pruneRunFile(BackupRun $run): ?string
    {
        $path = $run->bundle_path;

        if (blank($path) || ! file_exists($path)) {
            return null;
        }

        File::delete($path);
        $run->update([
            'bundle_path' => null,
            'meta' => array_merge($run->meta ?? [], [
                'pruned_at' => now()->toIso8601String(),
            ]),
        ]);

        return $path;
    }
}
