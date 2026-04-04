<?php

namespace App\Http\Middleware;

use App\Services\DisasterRecovery\NodeStateService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNodeIsActive
{
    public function __construct(protected NodeStateService $nodeState)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->nodeState->role() !== 'standby') {
            return $next($request);
        }

        if ($request->is('api/v1/dr/*') || $request->is('disaster-recovery/restore*') || $request->path() === 'up') {
            return $next($request);
        }

        if ($this->isLoopback($request->ip())) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'This node is in standby mode and is not serving client traffic.',
                'reason' => 'standby_node',
            ], 503);
        }

        abort(503, 'This node is in standby mode and is not serving client traffic.');
    }

    protected function isLoopback(?string $ip): bool
    {
        return in_array($ip, ['127.0.0.1', '::1'], true);
    }
}
