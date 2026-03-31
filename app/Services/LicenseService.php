<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * LicenseService
 *
 * Validates this terminal's license key against the enapel-cloud API.
 * Results are cached locally for the configured grace period to support
 * temporary offline usage.
 *
 * Cache key: license_payload
 */
class LicenseService
{
    private const CACHE_KEY = 'enapel_license_payload';

    /**
     * Get the current validated license payload.
     * Returns the cached value if still fresh, otherwise re-validates with cloud.
     */
    public function getPayload(bool $refresh = false): array
    {
        if ($refresh) {
            return $this->refresh();
        }

        return $this->normalizePayload(
            Cache::remember(
                self::CACHE_KEY,
                now()->addHours((int) config('license.grace_hours', 24)),
                fn() => $this->fetchFromCloud()
            )
        );
    }

    /**
     * Force a fresh validation ignoring the cache.
     */
    public function refresh(): array
    {
        Cache::forget(self::CACHE_KEY);

        $payload = $this->fetchFromCloud();

        Cache::put(
            self::CACHE_KEY,
            $payload,
            now()->addHours((int) config('license.grace_hours', 24))
        );

        return $this->normalizePayload($payload);
    }

    /**
     * Quick check: is the current cached/fresh payload valid?
     */
    public function isValid(bool $refresh = false): bool
    {
        return ($this->getPayload($refresh)['valid'] ?? false) === true;
    }

    /**
     * Get a specific value from the license payload.
     * e.g. $licenseService->get('tenant.name')
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $payload = $this->getPayload();
        return data_get($payload, $key, $default);
    }

    /**
     * Check if a specific module is unlocked by this license.
     * e.g. $licenseService->hasModule('pharmacy')
     */
    public function hasModule(string $module): bool
    {
        $modules = $this->get('modules', []);
        return in_array($module, $modules, strict: true);
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function normalizePayload(array $payload): array
    {
        $payload['license_configured'] = filled(config('license.key'))
            && filled(config('license.terminal_id'));
        $payload['is_expired'] = $this->isPayloadExpired($payload);

        if (($payload['valid'] ?? false) === true && $payload['is_expired']) {
            $payload['valid'] = false;
            $payload['reason'] = 'license_expired';
            $payload['message'] = 'This license has expired.';
        }

        return $payload;
    }

    private function isPayloadExpired(array $payload): bool
    {
        $expiryDate = data_get($payload, 'expiry_date');

        if (blank($expiryDate)) {
            return false;
        }

        try {
            return Carbon::now()->greaterThanOrEqualTo(Carbon::parse($expiryDate));
        } catch (\Throwable $e) {
            Log::warning('LicenseService: invalid expiry_date in payload.', [
                'expiry_date' => $expiryDate,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function fetchFromCloud(): array
    {
        $licenseKey   = config('license.key');
        $terminalId   = config('license.terminal_id');
        $terminalName = config('license.terminal_name');
        $cloudUrl     = rtrim(config('license.cloud_url'), '/');

        if (! $licenseKey || ! $terminalId) {
            Log::warning('LicenseService: LICENSE_KEY or TERMINAL_IDENTIFIER not set in .env');
            return $this->invalidPayload('configuration_missing', 'License key or terminal identifier not configured.');
        }

        try {
            $response = Http::timeout(10)
                ->withoutVerifying() // Bypass SSL issues in local/bundled environments
                ->post("{$cloudUrl}/api/v1/license/validate", [
                    'license_key'         => $licenseKey,
                    'terminal_identifier' => $terminalId,
                    'terminal_name'       => $terminalName,
                ]);

            if ($response->successful()) {
                $payload = $response->json();
                Log::info('License validated successfully.', ['license_key' => $licenseKey]);
                return $payload;
            }

            $body = $response->json();

            // If the key is invalid but we have a terminal ID, try to discover the correct key
            if (($body['reason'] ?? '') === 'license_not_found') {
                $discoveredKey = $this->discoverKey($terminalId, $cloudUrl);
                if ($discoveredKey) {
                    Log::info('LicenseService: Discovered new key from cloud. Retrying validation.', ['new_key' => $discoveredKey]);
                    // Update the local config/env for future requests
                    $this->updateLocalKey($discoveredKey);
                    // Retry with the new key
                    return $this->fetchFromCloud();
                }
            }

            Log::warning('License validation rejected by cloud.', $body ?? []);
            return $this->invalidPayload(
                $body['reason']  ?? 'cloud_rejected',
                $body['message'] ?? 'License validation failed.'
            );
        } catch (\Throwable $e) {
            Log::error('LicenseService: Cloud unreachable or connection error.', [
                'error' => $e->getMessage(),
                'url' => "{$cloudUrl}/api/v1/license/validate"
            ]);

            return $this->invalidPayload('cloud_unreachable', 'Could not connect to the licensing server.');
        }
    }

    /**
     * Try to find the correct license key for this terminal from the cloud.
     */
    private function discoverKey(string $terminalId, string $cloudUrl): ?string
    {
        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->post("{$cloudUrl}/api/v1/license/discover", [
                    'terminal_identifier' => $terminalId,
                ]);

            if ($response->successful()) {
                return $response->json('license_key');
            }
        } catch (\Throwable $e) {
            Log::warning('LicenseService: Discovery failed.', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Update the local .env file with the new license key.
     */
    private function updateLocalKey(string $key): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) return;

        $content = file_get_contents($envPath);
        if (str_contains($content, 'LICENSE_KEY=')) {
            $content = preg_replace('/^LICENSE_KEY=.*/m', "LICENSE_KEY={$key}", $content);
        } else {
            $content .= "\nLICENSE_KEY={$key}";
        }

        file_put_contents($envPath, $content);

        // Update current config so the rest of the request uses it
        config(['license.key' => $key]);
        \Illuminate\Support\Facades\Artisan::call('config:clear');
    }

    private function invalidPayload(string $reason, string $message): array
    {
        return [
            'valid'   => false,
            'reason'  => $reason,
            'message' => $message,
        ];
    }
}
