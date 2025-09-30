<?php

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsMahasiswa;
use App\Http\Middleware\IsMendaftar;
use App\Http\Middleware\IsVerifikator;
use App\Http\Middleware\ZonaPendaftar;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            IsMendaftar::class,
        ]);

        $middleware->alias([
            'zonaPendaftar' => ZonaPendaftar::class,
            'isAdmin' => IsAdmin::class,
            'isMahasiswa' => IsMahasiswa::class,
            'isVerifikator' => IsVerifikator::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();