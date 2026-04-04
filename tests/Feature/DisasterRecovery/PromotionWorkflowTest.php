<?php

namespace Tests\Feature\DisasterRecovery;

use App\Models\ReplicationCheckpoint;
use App\Services\DisasterRecovery\DisasterRecoveryService;
use App\Services\DisasterRecovery\NodeStateService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PromotionWorkflowTest extends TestCase
{
    protected string $sandboxPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sandboxPath = storage_path('framework/testing/dr-promotion-'.uniqid());
        File::ensureDirectoryExists($this->sandboxPath);
        $this->app->useStoragePath($this->sandboxPath.'/storage');
        File::ensureDirectoryExists(storage_path('app/public'));
        File::ensureDirectoryExists(storage_path('app/private'));

        $databasePath = $this->sandboxPath.'/database.sqlite';
        touch($databasePath);

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', $databasePath);
        DB::purge('sqlite');

        Artisan::call('migrate', ['--force' => true]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->sandboxPath);
        parent::tearDown();
    }

    public function test_promote_restores_latest_checkpoint_and_switches_role_to_primary(): void
    {
        File::put(storage_path('app/public/app-state.txt'), 'ready');

        $service = app(DisasterRecoveryService::class);
        $service->saveSettings([
            'node_name' => 'Standby',
            'node_role' => 'standby',
            'nas_path' => $this->sandboxPath.'/nas',
            'service_hostname' => 'standby.local',
            'cloud_mirror_enabled' => false,
            'snapshot_interval_minutes' => 15,
            'full_backup_hour' => 2,
            'monthly_backup_hour' => 3,
            'retention_snapshot_days' => 7,
            'retention_daily_backups' => 30,
            'retention_monthly_backups' => 12,
            'standby_enabled' => true,
            'standby_primary_url' => 'http://primary.local:8000',
            'passphrase' => 'test-passphrase-123',
            'passphrase_hint' => 'qa',
        ]);

        app(NodeStateService::class)->setRole('standby');

        $run = $service->runSnapshot('daily', true);

        ReplicationCheckpoint::create([
            'checkpoint_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'type' => 'daily',
            'status' => 'applied',
            'source_bundle_uuid' => $run->bundle_uuid,
            'source_bundle_path' => $run->bundle_path,
            'sync_age_seconds' => 10,
            'applied_at' => now(),
            'manifest' => $run->manifest,
        ]);

        $result = $service->promote();

        $this->assertSame('promoted', $result['status']);
        $this->assertSame('primary', app(NodeStateService::class)->role());
        $this->assertSame('primary', $service->settings()->fresh()->node_role);
    }
}
