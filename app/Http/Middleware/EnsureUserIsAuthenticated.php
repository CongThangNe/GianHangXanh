<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAuthenticated
{
    /**
     * Đảm bảo người dùng đã đăng nhập trước khi tiếp tục.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {

            // Nếu là request AJAX / API thì trả JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Bạn cần đăng nhập trước khi thanh toán.',
                ], 401);
            }

            // ❌ Không dùng route('login') nữa
            // ✔ Chuyển về trang chủ theo Cách 1
            return redirect('/')->withErrors([
                'auth' => 'Vui lòng đăng nhập trước khi tiếp tục thanh toán.',
            ]);
        }

        return $next($request);
    }
}
