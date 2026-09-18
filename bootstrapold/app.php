<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api'
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // ✅ API middleware (NO CSRF)
        $middleware->api([
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // ✅ Spatie role middleware aliases
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'check.permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })

    // ->withMiddleware(function ($middleware) {
    //     $middleware->alias([
    //         'check.permission' => \App\Http\Middleware\CheckPermission::class,
    //     ]);
    // })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
