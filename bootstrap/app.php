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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'update.last.activity' => \App\Http\Middleware\UpdateLastActivity::class,
            'check.subscription' => \App\Http\Middleware\CheckSubscription::class,
        ]);

        // Middleware para web
        $middleware->web(append: [
            \App\Http\Middleware\UpdateLastActivity::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
