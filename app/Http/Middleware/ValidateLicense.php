<?php

namespace App\Http\Middleware;

use App\Services\LicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ValidateLicense
 *
 * Applied to the 'auth' middleware group. Checks that this terminal has a
 * valid, cloud-confirmed license before allowing access to the application.
 *
 * If the license is invalid, the user is redirected to /license-required.
 * The grace period in LicenseService means offline terminals still work
 * for up to N hours without re-contacting the cloud.
 */
class ValidateLicense
{
    private LicenseService $license;

    public function __construct(LicenseService $license)
    {
        $this->license = $license;
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Skip license check on the license-required page itself to avoid redirect loops
        if ($request->routeIs('license.required')) {
            return $next($request);
        }

        $payload = $this->license->getPayload();

        if (($payload['valid'] ?? false) !== true) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => $payload['message'] ?? 'Your license is not valid.',
                    'reason' => $payload['reason'] ?? 'license_invalid',
                    'license' => $payload,
                ], 403);
            }

            return redirect()->route('license.required')->with([
                'license_error'  => true,
                'license_reason' => $payload['reason']  ?? 'unknown',
                'license_message' => $payload['message'] ?? 'Your license is not valid.',
            ]);
        }

        return $next($request);
    }
}
