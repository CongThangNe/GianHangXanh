<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\CartNotEmpty;
use App\Http\Middleware\CheckCartDB;
use App\Http\Middleware\CartAuth;
use App\Http\Middleware\AdminAccess;
use App\Http\Middleware\AdminOnly;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Đăng ký middleware alias theo chuẩn Laravel 12
        $middleware->alias([
            'cart_auth' => CartAuth::class,
            'cart_notempty' => CartNotEmpty::class,
            'check_cart_db' => CheckCartDB::class,
            'admin_access' => AdminAccess::class,
            'admin_only' => AdminOnly::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
