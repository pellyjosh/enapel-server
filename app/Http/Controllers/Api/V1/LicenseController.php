<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LicenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    private LicenseService $license;

    public function __construct(LicenseService $license)
    {
        $this->license = $license;
    }

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
