<?php

namespace App\Services\DisasterRecovery;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class NodeStateService
{
    public function path(): string
    {
        return storage_path('app/private/dr/node.json');
    }

    public function get(): array
    {
        $path = $this->path();

        if (! File::exists($path)) {
            $state = $this->defaults();
            $this->save($state);

            return $state;
        }

        $payload = File::get($path);

        if ($payload === false || trim($payload) === '') {
            $state = $this->defaults();
            $this->save($state);

            return $state;
        }

        $decoded = json_decode($payload, true);

        if (is_array($decoded) && isset($decoded['ciphertext'])) {
            $json = Crypt::decryptString($decoded['ciphertext']);
            $state = json_decode($json, true);

            return is_array($state) ? array_replace_recursive($this->defaults(), $state) : $this->defaults();
        }

        return is_array($decoded) ? array_replace_recursive($this->defaults(), $decoded) : $this->defaults();
    }

    public function save(array $state): array
    {
        File::ensureDirectoryExists(dirname($this->path()));

        $normalized = array_replace_recursive($this->defaults(), $state);
        $ciphertext = Crypt::encryptString(json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        File::put($this->path(), json_encode([
            'version' => 1,
            'ciphertext' => $ciphertext,
            'updated_at' => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $normalized;
    }

    public function update(array $attributes): array
    {
        return $this->save(array_replace_recursive($this->get(), $attributes));
    }

    public function role(): string
    {
        return Arr::get($this->get(), 'role', 'primary');
    }

    public function setRole(string $role): array
    {
        return $this->update([
            'role' => $role,
            'role_changed_at' => now()->toIso8601String(),
        ]);
    }

    public function sharedSecret(): ?string
    {
        return Arr::get($this->get(), 'shared_secret');
    }

    public function primaryUrl(): ?string
    {
        return Arr::get($this->get(), 'primary_url');
    }

    public function defaults(): array
    {
        return [
            'node_uuid' => (string) Str::uuid(),
            'node_name' => gethostname() ?: 'enapel-node',
            'role' => 'primary',
            'pairing' => [
                'token' => null,
                'paired_at' => null,
                'primary_node_uuid' => null,
            ],
            'primary_url' => null,
            'shared_secret' => null,
            'backup_paths' => [],
            'last_promoted_snapshot' => null,
            'last_restored_bundle' => null,
            'last_sync_at' => null,
            'last_sync_bundle_uuid' => null,
            'replication_paused' => false,
            'role_changed_at' => null,
        ];
    }
}
