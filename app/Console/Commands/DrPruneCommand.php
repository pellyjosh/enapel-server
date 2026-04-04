<?php

namespace App\Console\Commands;

use App\Services\DisasterRecovery\DisasterRecoveryService;
use Illuminate\Console\Command;

class DrPruneCommand extends Command
{
    protected $signature = 'dr:prune';

    protected $description = 'Prune expired disaster recovery bundles based on retention policy.';

    public function handle(DisasterRecoveryService $recovery): int
    {
        $deleted = $recovery->prune();
        $this->info('Pruned '.count($deleted).' bundle(s).');

        return self::SUCCESS;
    }
}
