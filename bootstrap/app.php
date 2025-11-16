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
        // Đăng ký alias middleware cho toàn ứng dụng
        $middleware->alias([
            // Kiểm tra giỏ hàng không rỗng trước khi thanh toán
            'cart.notempty' => \App\Http\Middleware\CheckCartNotEmpty::class,

            // Bắt buộc người dùng đăng nhập khi checkout
            'checkout.auth' => \App\Http\Middleware\EnsureUserIsAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
