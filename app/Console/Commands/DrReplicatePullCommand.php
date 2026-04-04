<?php

namespace App\Console\Commands;

use App\Services\DisasterRecovery\ReplicationService;
use Illuminate\Console\Command;

class DrReplicatePullCommand extends Command
{
    protected $signature = 'dr:replicate-pull {--iterations=1} {--interval=15}';

    protected $description = 'Pull disaster recovery bundles from the paired primary node.';

    public function handle(ReplicationService $replication): int
    {
        $result = $replication->pullAvailableBundles(
            iterations: max(1, (int) $this->option('iterations')),
            intervalSeconds: max(0, (int) $this->option('interval'))
        );

        $this->line(json_encode($result, JSON_UNESCAPED_SLASHES));

        return self::SUCCESS;
    }
}
