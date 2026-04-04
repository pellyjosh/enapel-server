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
        $run = $recovery->runSnapshot(
            type: (string) $this->option('type'),
            full: (bool) $this->option('full'),
            passphrase: $this->option('passphrase') ?: null,
        );

        $this->info("Created {$run->type} bundle {$run->bundle_uuid}");

        return self::SUCCESS;
    }
}
