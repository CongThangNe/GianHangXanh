<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Cart;

class CheckCartDB
{
    public function handle(Request $request, Closure $next)
    {
        // Lấy session hiện tại
        $sessionId = session()->getId();

        // Tìm giỏ hàng theo session_id
        $cart = Cart::where('session_id', $sessionId)
                    ->with('items')
                    ->first();

        // Nếu không có giỏ hoặc giỏ không có sản phẩm
        if (!$cart || $cart->items->count() === 0) {
            return response()->json([
                'status' => false,
                'message' => 'Không có sản phẩm trong giỏ hàng để thanh toán.',
            ], 400);
        }

        return $next($request);
    }
}
