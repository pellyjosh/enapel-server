<?php

namespace App\Console\Commands;

use App\Services\DisasterRecovery\DisasterRecoveryService;
use Illuminate\Console\Command;

class DrPromoteCommand extends Command
{
    protected $signature = 'dr:promote {--passphrase=}';

    protected $description = 'Promote the local standby node to primary.';

    public function handle(DisasterRecoveryService $recovery): int
    {
        $result = $recovery->promote($this->option('passphrase') ?: null);
        $this->line(json_encode($result, JSON_UNESCAPED_SLASHES));

        return self::SUCCESS;
    }
}
