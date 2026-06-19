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
        // Tambahkan middleware admin di sini
        $middleware->alias([
            'admin' => App\Http\Middleware\AdminMiddleware::class,
        ]);
        
        // Jika perlu menambahkan middleware ke group, bisa dilakukan di sini
        // $middleware->appendToGroup('web', [
        //     // middleware tambahan
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();