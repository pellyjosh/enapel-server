<?php

namespace App\Services\DisasterRecovery;

use RuntimeException;

class BundleCryptoService
{
    public function encryptFile(string $sourcePath, string $destinationPath, string $passphrase, array $header = []): array
    {
        $plaintext = file_get_contents($sourcePath);

        if ($plaintext === false) {
            throw new RuntimeException("Unable to read bundle source [{$sourcePath}].");
        }

        $salt = random_bytes(32);
        $iv = random_bytes(12);
        $key = $this->deriveKey($passphrase, $salt);
        $tag = '';

        $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

        if ($ciphertext === false) {
            throw new RuntimeException('Unable to encrypt backup bundle.');
        }

        $envelope = [
            'version' => 1,
            'cipher' => 'aes-256-gcm',
            'header' => $header,
            'salt' => base64_encode($salt),
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
            'checksum' => hash('sha256', $plaintext),
            'payload' => base64_encode($ciphertext),
        ];

        if (file_put_contents($destinationPath, json_encode($envelope, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
            throw new RuntimeException("Unable to write encrypted bundle [{$destinationPath}].");
        }

        return $envelope['header'];
    }

    public function decryptFile(string $bundlePath, string $destinationPath, string $passphrase): array
    {
        $envelope = $this->readEnvelope($bundlePath);

        $payload = base64_decode($envelope['payload'], true);
        $salt = base64_decode($envelope['salt'], true);
        $iv = base64_decode($envelope['iv'], true);
        $tag = base64_decode($envelope['tag'], true);

        if ($payload === false || $salt === false || $iv === false || $tag === false) {
            throw new RuntimeException("Encrypted bundle [{$bundlePath}] is malformed.");
        }

        $plaintext = openssl_decrypt(
            $payload,
            $envelope['cipher'] ?? 'aes-256-gcm',
            $this->deriveKey($passphrase, $salt),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($plaintext === false) {
            throw new RuntimeException('Unable to decrypt backup bundle. The passphrase may be wrong.');
        }

        if (hash('sha256', $plaintext) !== ($envelope['checksum'] ?? '')) {
            throw new RuntimeException('Backup bundle checksum verification failed.');
        }

        if (file_put_contents($destinationPath, $plaintext) === false) {
            throw new RuntimeException("Unable to write decrypted archive [{$destinationPath}].");
        }

        return $envelope['header'] ?? [];
    }

    public function readHeader(string $bundlePath): array
    {
        $envelope = $this->readEnvelope($bundlePath);

        return $envelope['header'] ?? [];
    }

    protected function readEnvelope(string $bundlePath): array
    {
        $raw = file_get_contents($bundlePath);

        if ($raw === false) {
            throw new RuntimeException("Unable to read bundle [{$bundlePath}].");
        }

        $decoded = json_decode($raw, true);

        if (! is_array($decoded) || ! isset($decoded['payload'])) {
            throw new RuntimeException("Bundle [{$bundlePath}] is not a valid DR bundle.");
        }

        return $decoded;
    }

    protected function deriveKey(string $passphrase, string $salt): string
    {
        return hash_pbkdf2('sha256', $passphrase, $salt, 200000, 32, true);
    }
}
