<?php

namespace App\Services\DisasterRecovery;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class SignatureService
{
    public function makeHeaders(string $secret, string $nodeUuid, string $method, string $path, array|string|null $body = null): array
    {
        $timestamp = (string) CarbonImmutable::now()->timestamp;
        $payload = is_array($body) ? json_encode($body, JSON_UNESCAPED_SLASHES) : (string) ($body ?? '');
        $signature = hash_hmac('sha256', $this->signaturePayload($method, $path, $timestamp, $payload), $secret);

        return [
            'X-Dr-Node' => $nodeUuid,
            'X-Dr-Timestamp' => $timestamp,
            'X-Dr-Signature' => $signature,
        ];
    }

    public function validate(Request $request, string $secret): void
    {
        $timestamp = (string) $request->header('X-Dr-Timestamp', '');
        $signature = (string) $request->header('X-Dr-Signature', '');

        if ($timestamp === '' || $signature === '') {
            throw new RuntimeException('Missing DR signature headers.');
        }

        if (abs(now()->timestamp - (int) $timestamp) > config('disaster-recovery.signature_ttl_seconds', 300)) {
            throw new RuntimeException('DR request signature has expired.');
        }

        $payload = $this->signaturePayload(
            $request->method(),
            '/'.$request->path(),
            $timestamp,
            $request->getContent()
        );

        $expected = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expected, $signature)) {
            throw new RuntimeException('DR request signature is invalid.');
        }
    }

    public function hashPairToken(string $token): string
    {
        return Hash::make($token);
    }

    public function checkPairToken(string $plain, ?string $hash): bool
    {
        return filled($plain) && filled($hash) && Hash::check($plain, $hash);
    }

    protected function signaturePayload(string $method, string $path, string $timestamp, string $body): string
    {
        return strtoupper($method)."\n".$path."\n".$timestamp."\n".hash('sha256', $body);
    }
}
