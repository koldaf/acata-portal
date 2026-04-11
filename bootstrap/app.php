<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\ActivityLogMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\DuesEnforcementMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'auth' => Authenticate::class,
        'admin' => AdminMiddleware::class,
        'activity.log' => ActivityLogMiddleware::class,
        'dues.paid' => DuesEnforcementMiddleware::class,
    ]);
})
->withExceptions(function (Exceptions $exceptions): void {
    //
})->create();