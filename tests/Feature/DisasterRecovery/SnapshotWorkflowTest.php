<?php

namespace Tests\Feature\DisasterRecovery;

use App\Services\DisasterRecovery\DisasterRecoveryService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SnapshotWorkflowTest extends TestCase
{
    protected string $sandboxPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sandboxPath = storage_path('framework/testing/dr-snapshot-'.uniqid());
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

    public function test_snapshot_creates_encrypted_bundle_on_nas(): void
    {
        File::put(storage_path('app/public/catalog.txt'), 'public payload');
        File::put(storage_path('app/private/license.json'), '{"secret":true}');

        $nasPath = $this->sandboxPath.'/nas';
        $service = app(DisasterRecoveryService::class);
        $service->saveSettings([
            'node_name' => 'Primary',
            'node_role' => 'primary',
            'nas_path' => $nasPath,
            'service_hostname' => 'primary.local',
            'cloud_mirror_enabled' => false,
            'snapshot_interval_minutes' => 15,
            'full_backup_hour' => 2,
            'monthly_backup_hour' => 3,
            'retention_snapshot_days' => 7,
            'retention_daily_backups' => 30,
            'retention_monthly_backups' => 12,
            'standby_enabled' => false,
            'standby_primary_url' => null,
            'passphrase' => 'test-passphrase-123',
            'passphrase_hint' => 'qa',
        ]);

        $run = $service->runSnapshot('snapshot', true);

        $this->assertSame('completed', $run->status);
        $this->assertFileExists($run->bundle_path);
        $this->assertNotEmpty($run->manifest['public/catalog.txt']);
        $this->assertNotEmpty($run->manifest['private/license.json']);
        $this->assertCount(1, $service->availableNasBundles($nasPath));
    }
}
