<?php
// bootstrap/app.php

use App\Http\Middleware\CheckPeriodeActive;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->validateCsrfTokens(except: [
            'api/sipintu/*',
            'sipintu/*',
        ]);
        $middleware->web(append: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':web-global',
        ]);
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'periode.active' => CheckPeriodeActive::class,
            'siswa.maintenance' => \App\Http\Middleware\CheckSiswaMaintenance::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();