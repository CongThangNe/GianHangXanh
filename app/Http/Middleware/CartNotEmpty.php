<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CartNotEmpty
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $sessionId = session()->getId();
        $cart = \App\Models\Cart::where('session_id', $sessionId)->with('items')->first();

        // Kiểm tra giỏ có rỗng không
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Giỏ hàng của bạn đang trống! Vui lòng thêm sản phẩm trước khi thanh toán.');
        }

        return $next($request);
    }
}
