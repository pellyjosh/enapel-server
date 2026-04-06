<?php

namespace App\Console\Commands;

use App\Services\DisasterRecovery\DisasterRecoveryService;
use Illuminate\Console\Command;

class DrSnapshotCommand extends Command
{
    protected $signature = 'dr:snapshot {--type=snapshot} {--full} {--passphrase=}';

    protected $description = 'Create an encrypted disaster recovery snapshot bundle.';

    public function handle(DisasterRecoveryService $recovery): int
    {
        try {
            $run = $recovery->runSnapshot(
                type: (string) $this->option('type'),
                full: (bool) $this->option('full'),
                passphrase: $this->option('passphrase') ?: null,
            );

            $this->info("Created {$run->type} bundle {$run->bundle_uuid}");

            return self::SUCCESS;
        } catch (\RuntimeException $e) {
            // Disaster Recovery is not yet configured (e.g. no NAS path set).
            // Exit gracefully so the scheduler does not log a false alarm.
            $this->line("<fg=gray>dr:snapshot skipped: {$e->getMessage()}</>");

            return self::SUCCESS;
        }
    }
}
