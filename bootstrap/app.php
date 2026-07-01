<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'session.security' => \App\Http\Middleware\Security\SessionSecurityMiddleware::class,
            'otp' => \App\Http\Middleware\Security\OtpMiddleware::class,
            'no.cache' => \App\Http\Middleware\Security\NoCacheMiddleware::class,                
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
