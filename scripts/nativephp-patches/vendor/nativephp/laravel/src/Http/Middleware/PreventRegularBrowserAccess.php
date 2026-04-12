<?php

namespace Native\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventRegularBrowserAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (! config('nativephp-internal.running')) {
            return $next($request);
        }

        if ($this->shouldAllowLanAccess($request)) {
            return $next($request);
        }

        // Explicitly skip for the cookie-setting route
        if ($request->path() === '_native/api/cookie') {
            return $next($request);
        }

        // Allow access to public storage assets and other public files
        if ($request->is('storage/*', 'build/*', 'assets/*', 'wizard/*', 'favicon.ico')) {
            return $next($request);
        }

        $cookie = $request->cookie('_php_native');
        $header = $request->header('X-NativePHP-Secret');

        if ($cookie && $cookie === config('nativephp-internal.secret')) {
            return $next($request);
        }

        if ($header && $header === config('nativephp-internal.secret')) {
            return $next($request);
        }

        return abort(403);
    }

    private function shouldAllowLanAccess(Request $request): bool
    {
        if (! config('nativephp.allow_lan', false)) {
            return false;
        }

        return $this->isPrivateIp($request->ip());
    }

    private function isPrivateIp(?string $ip): bool
    {
        if (! $ip) {
            return false;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ipLong = ip2long($ip);

            if ($ipLong === false) {
                return false;
            }

            return ($ipLong >= ip2long('10.0.0.0') && $ipLong <= ip2long('10.255.255.255'))
                || ($ipLong >= ip2long('172.16.0.0') && $ipLong <= ip2long('172.31.255.255'))
                || ($ipLong >= ip2long('192.168.0.0') && $ipLong <= ip2long('192.168.255.255'))
                || ($ipLong >= ip2long('127.0.0.0') && $ipLong <= ip2long('127.255.255.255'));
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $normalized = strtolower($ip);

            if ($normalized === '::1') {
                return true;
            }

            return str_starts_with($normalized, 'fc')
                || str_starts_with($normalized, 'fd')
                || str_starts_with($normalized, 'fe80');
        }

        return false;
    }
}
