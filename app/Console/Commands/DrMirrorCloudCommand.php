<?php

namespace App\Console\Commands;

use App\Models\BackupRun;
use App\Services\DisasterRecovery\DisasterRecoveryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DrMirrorCloudCommand extends Command
{
    protected $signature = 'dr:mirror-cloud';

    protected $description = 'Mirror completed NAS bundles to the configured cloud endpoint.';

    public function handle(DisasterRecoveryService $recovery): int
    {
        $settings = $recovery->settings();

        if (! $settings->cloud_mirror_enabled || blank($settings->cloud_mirror_url)) {
            $this->comment('Cloud mirroring is not configured.');

            return self::SUCCESS;
        }

        $pending = BackupRun::query()
            ->where('status', BackupRun::STATUS_COMPLETED)
            ->whereNull('mirrored_at')
            ->whereNotNull('bundle_path')
            ->orderBy('completed_at')
            ->get();

        foreach ($pending as $run) {
            if (! $run->bundle_path || ! file_exists($run->bundle_path)) {
                continue;
            }

            $response = Http::timeout(config('disaster-recovery.cloud_timeout_seconds', 30))
                ->acceptJson()
                ->when(filled($settings->cloud_mirror_token), fn ($request) => $request->withToken($settings->cloud_mirror_token))
                ->attach('bundle', file_get_contents($run->bundle_path), basename($run->bundle_path))
                ->post($settings->cloud_mirror_url, [
                    'bundle_uuid' => $run->bundle_uuid,
                    'type' => $run->type,
                    'checksum' => $run->checksum,
                ]);

            if (! $response->successful()) {
                $this->warn("Cloud mirror failed for {$run->bundle_uuid}");
                continue;
            }

            $run->update([
                'status' => BackupRun::STATUS_MIRRORED,
                'mirrored_at' => now(),
                'cloud_bundle_url' => $response->json('url'),
            ]);

            $settings->update([
                'last_cloud_mirror_at' => now(),
            ]);
        }

        return self::SUCCESS;
    }
}
