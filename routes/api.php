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
    $terminalId = config('license.terminal_id') ?: (string) \Illuminate\Support\Str::uuid();

    try {
        $response = Http::timeout(10)->post("{$cloudUrl}/api/v1/license/validate", [
            'license_key'         => strtoupper(trim($request->license_key)),
            'terminal_identifier' => $terminalId,
            'terminal_name'       => 'Initial Setup',
        ]);

        return response()->json($response->json(), $response->status());
    } catch (\Throwable $e) {
        return response()->json([
            'valid'   => false,
            'message' => 'Could not connect to the licensing server. Please try again.',
            'reason'  => 'cloud_unreachable',
        ], 503);
    }
})->middleware('throttle:10,1');
