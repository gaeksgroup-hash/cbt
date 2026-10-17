<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureCbtAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        App\Console\Commands\ImportSakBank::class,
        App\Console\Commands\RegradeAutomaticAttempts::class,
        App\Console\Commands\ValidateSakBank::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'cbt.admin' => EnsureCbtAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
