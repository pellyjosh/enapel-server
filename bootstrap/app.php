<?php

use App\Support\RuntimeEnvironment;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\EnsureNodeIsActive::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\EnsureNodeIsActive::class,
        ]);
        $middleware->alias([
            'validate.license' => \App\Http\Middleware\ValidateLicense::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

if ($environmentPath = RuntimeEnvironment::initialize(dirname(__DIR__))) {
    $app->useEnvironmentPath($environmentPath);
}

return $app;
