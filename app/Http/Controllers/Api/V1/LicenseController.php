<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LicenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function __construct(private readonly LicenseService $license) {}

    public function status(Request $request): JsonResponse
    {
        $payload = $request->boolean('refresh')
            ? $this->license->refresh()
            : $this->license->getPayload();

        return response()->json(array_merge([
            'configured' => (bool) ($payload['license_configured'] ?? false),
            'terminal_identifier' => config('license.terminal_id'),
            'terminal_name' => config('license.terminal_name'),
        ], $payload));
    }
}
