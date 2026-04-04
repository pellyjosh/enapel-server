<?php

namespace App\Services\DisasterRecovery;

use App\Support\RuntimeEnvironment;
use App\Models\DisasterRecoverySetting;
use Illuminate\Support\Str;

class EnvBundleService
{
    public function capture(DisasterRecoverySetting $settings): array
    {
        $env = $this->readEnv();
        $bundle = [];

        $explicitKeys = config('disaster-recovery.env_keys', []);
        $prefixes = config('disaster-recovery.env_key_prefixes', []);

        foreach ($env as $key => $value) {
            if (in_array($key, $explicitKeys, true) || $this->matchesPrefix($key, $prefixes)) {
                $bundle[$key] = $value;
            }
        }

        $bundle['_disaster_recovery'] = [
            'service_hostname' => $settings->service_hostname,
            'node_role' => $settings->node_role,
            'snapshot_interval_minutes' => $settings->snapshot_interval_minutes,
            'retention_snapshot_days' => $settings->retention_snapshot_days,
            'retention_daily_backups' => $settings->retention_daily_backups,
            'retention_monthly_backups' => $settings->retention_monthly_backups,
            'cloud_mirror_enabled' => $settings->cloud_mirror_enabled,
            'cloud_mirror_url' => $settings->cloud_mirror_url,
            'standby_enabled' => $settings->standby_enabled,
            'standby_primary_url' => $settings->standby_primary_url,
        ];

        return $bundle;
    }

    public function restore(array $bundle): void
    {
        unset($bundle['_disaster_recovery']);

        $env = $this->readEnv();

        foreach ($bundle as $key => $value) {
            $env[$key] = is_scalar($value) || $value === null ? (string) $value : json_encode($value);
        }

        $this->writeEnv($env);
    }

    protected function readEnv(): array
    {
        $path = RuntimeEnvironment::environmentFilePath();

        if (! file_exists($path)) {
            return [];
        }

        $contents = file($path, FILE_IGNORE_NEW_LINES);
        $values = [];

        foreach ($contents ?: [] as $line) {
            $trimmed = trim($line);

            if ($trimmed === '' || Str::startsWith($trimmed, '#') || ! str_contains($trimmed, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $trimmed, 2);
            $values[$key] = trim($value, "\"'");
        }

        return $values;
    }

    protected function writeEnv(array $env): void
    {
        ksort($env);

        $lines = [];

        foreach ($env as $key => $value) {
            $formatted = preg_match('/\s/', (string) $value) ? '"'.str_replace('"', '\"', (string) $value).'"' : (string) $value;
            $lines[] = "{$key}={$formatted}";
        }

        file_put_contents(RuntimeEnvironment::environmentFilePath(), implode(PHP_EOL, $lines).PHP_EOL);
    }

    protected function matchesPrefix(string $key, array $prefixes): bool
    {
        foreach ($prefixes as $prefix) {
            if (Str::startsWith($key, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
