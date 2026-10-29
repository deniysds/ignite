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
        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard',
        );
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \Modules\Usermanagement\Http\Middleware\EnforceSessionInactivityTimeout::class,
            \Modules\Usermanagement\Http\Middleware\EnsurePasswordNotExpired::class,
            \Modules\Usermanagement\Http\Middleware\EnforceLogonSchedule::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
