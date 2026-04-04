<?php

namespace Tests\Feature\DisasterRecovery;

use App\Http\Middleware\ValidateLicense;
use App\Models\DisasterRecoverySetting;
use App\Models\User;
use App\Services\DisasterRecovery\NodeStateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisasterRecoverySettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_disaster_recovery_settings_page(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->withoutMiddleware(ValidateLicense::class)
            ->actingAs($user)
            ->get(route('global.settings.disaster-recovery'));

        $response->assertOk();
    }

    public function test_settings_update_persists_database_and_local_node_state(): void
    {
        $user = User::factory()->create();

        $payload = [
            'node_name' => 'Standby-01',
            'node_role' => 'standby',
            'service_hostname' => 'enapel-dr.local',
            'nas_path' => storage_path('framework/testing/nas'),
            'cloud_mirror_enabled' => true,
            'cloud_mirror_url' => 'https://mirror.example.test/upload',
            'cloud_mirror_token' => 'mirror-token',
            'snapshot_interval_minutes' => 15,
            'full_backup_hour' => 2,
            'monthly_backup_hour' => 3,
            'retention_snapshot_days' => 7,
            'retention_daily_backups' => 30,
            'retention_monthly_backups' => 12,
            'standby_enabled' => true,
            'standby_primary_url' => 'http://primary.local:8000',
            'passphrase' => 'test-passphrase-123',
            'passphrase_confirmation' => 'test-passphrase-123',
            'passphrase_hint' => 'office vault',
        ];

        $this
            ->withoutMiddleware(ValidateLicense::class)
            ->actingAs($user)
            ->put(route('global.settings.disaster-recovery.update'), $payload)
            ->assertRedirect();

        $settings = DisasterRecoverySetting::current();

        $this->assertSame('standby', $settings->node_role);
        $this->assertSame('enapel-dr.local', $settings->service_hostname);
        $this->assertTrue($settings->cloud_mirror_enabled);
        $this->assertNotEmpty($settings->encrypted_passphrase);

        $nodeState = app(NodeStateService::class)->get();

        $this->assertSame('standby', $nodeState['role']);
        $this->assertSame('Standby-01', $nodeState['node_name']);
    }
}
