<?php

namespace Tests\Feature\License;

use App\Models\User;
use App\Services\LicenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseEnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_is_blocked_when_license_is_invalid(): void
    {
        $this->app->instance(LicenseService::class, new class extends LicenseService
        {
            public function getPayload(bool $refresh = false): array
            {
                return [
                    'valid' => false,
                    'reason' => 'license_expired',
                    'message' => 'This license has expired.',
                ];
            }
        });

        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response
            ->assertForbidden()
            ->assertJsonPath('reason', 'license_expired')
            ->assertJsonPath('license.valid', false);
    }

    public function test_license_status_endpoint_returns_the_local_license_payload(): void
    {
        $this->app->instance(LicenseService::class, new class extends LicenseService
        {
            public function getPayload(bool $refresh = false): array
            {
                return [
                    'valid' => true,
                    'reason' => null,
                    'message' => null,
                    'license_configured' => true,
                    'expiry_date' => now()->addDay()->toIso8601String(),
                ];
            }
        });

        $response = $this->getJson('/api/v1/license/status');

        $response
            ->assertOk()
            ->assertJsonPath('valid', true)
            ->assertJsonPath('configured', true);
    }

    public function test_web_dashboard_redirects_to_license_required_when_license_is_invalid(): void
    {
        $this->app->instance(LicenseService::class, new class extends LicenseService
        {
            public function getPayload(bool $refresh = false): array
            {
                return [
                    'valid' => false,
                    'reason' => 'configuration_missing',
                    'message' => 'License key or terminal identifier not configured.',
                ];
            }
        });

        $user = new User();
        $user->id = 1;
        $user->name = 'Test User';
        $user->email = 'test@example.com';
        $user->email_verified_at = now();

        $response = $this->actingAs($user)->get('/dashboard');

        $response
            ->assertRedirect(route('license.required'))
            ->assertSessionHas('license_reason', 'configuration_missing');
    }
}
