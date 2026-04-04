<?php

namespace App\Console\Commands;

use App\Services\DisasterRecovery\DisasterRecoveryService;
use App\Services\DisasterRecovery\NodeStateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class DrRestoreCommand extends Command
{
    protected $signature = 'dr:restore {--nas-path=} {--bundle=} {--bundle-uuid=} {--download-url=} {--passphrase=} {--role=primary}';

    protected $description = 'Restore the local node from a NAS or cloud disaster recovery bundle.';

    public function handle(DisasterRecoveryService $recovery, NodeStateService $nodeState): int
    {
        $passphrase = (string) ($this->option('passphrase') ?: '');

        if ($passphrase === '') {
            $this->error('A DR passphrase is required for restore.');

            return self::FAILURE;
        }

        if ($bundle = $this->option('bundle')) {
            $recovery->applyBundle($bundle, $passphrase, [
                'restore_env' => true,
                'run_migrations' => true,
            ]);
        } elseif ($downloadUrl = $this->option('download-url')) {
            $workspace = storage_path('app/private/dr/tmp/manual-restore');
            File::ensureDirectoryExists($workspace);
            $downloadPath = $workspace.DIRECTORY_SEPARATOR.'cloud-bundle.drb';
            File::put($downloadPath, Http::timeout(120)->get($downloadUrl)->body());

            $recovery->applyBundle($downloadPath, $passphrase, [
                'restore_env' => true,
                'run_migrations' => true,
            ]);
        } else {
            $nasPath = $this->option('nas-path') ?: $recovery->settings()->nas_path;
            $recovery->restoreFromNas($nasPath, $passphrase, $this->option('bundle-uuid') ?: null);
        }

        $role = (string) $this->option('role');
        $nodeState->setRole($role);
        $recovery->saveSettings(['node_role' => $role]);

        $this->info("Restore complete. Node role set to {$role}.");

        return self::SUCCESS;
    }
}
