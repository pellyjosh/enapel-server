<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->post('/activity-logs', [App\Http\Controllers\Api\ActivityLogController::class, 'store']);

Route::prefix('v1')->group(base_path('routes/api/api_v1.php'));

// ─── License validation proxy (avoids browser CORS issues) ────────────────────
Route::post('/license/validate-key', function (Request $request) {
    $request->validate(['license_key' => 'required|string|min:5']);

    $cloudUrl = rtrim(config('license.cloud_url'), '/');
    $terminalId = config('license.terminal_id');
    
    if (!$terminalId) {
        $terminalId = (string) \Illuminate\Support\Str::uuid();
        // ─── Consolidate Configuration: Save to .env ───
        $licenseService = app(\App\Services\LicenseService::class);
        $licenseService->updateLocalEnv('LICENSE_KEY', $request->license_key);
        $licenseService->updateLocalEnv('TERMINAL_IDENTIFIER', $terminalId);

        config([
            'license.key' => $request->license_key,
            'license.terminal_id' => $terminalId,
        ]);

        \Illuminate\Support\Facades\Log::info('Register: Configuration persisted to .env');

        // Now refresh the license service with the configured key
        $licenseService->refresh();
    }

    try {
        $response = Http::timeout(10)
            ->withoutVerifying()
            ->post("{$cloudUrl}/api/v1/license/validate", [
                'license_key'         => strtoupper(trim($request->license_key)),
                'terminal_identifier' => $terminalId,
                'terminal_name'       => 'Initial Setup',
            ]);

        $data = $response->json();
        if (($data['valid'] ?? false) === true) {
            $email = $data['tenant']['owner_email'] ?? null;
            if ($email && \App\Models\User::where('email', $email)->exists()) {
                $data['already_activated'] = true;
                $data['activated_email'] = $email;
            }
        }

        return response()->json($data, $response->status());
    } catch (\Throwable $e) {
        return response()->json([
            'valid'   => false,
            'message' => 'Could not connect to the licensing server. Please try again.',
            'reason'  => 'cloud_unreachable',
        ], 503);
    }
})->middleware('throttle:10,1');
