<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartAuth
{
    /**
     * Require authentication for cart actions.
     *
     * - For GET /cart: remember the cart URL so user returns to cart after login.
     * - For POST actions (add/update/remove...): remember previous page (product page)
     *   so user returns to the page they were on after login.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }

        // Store intended URL smartly to avoid redirecting back to a POST route.
        if ($request->isMethod('get')) {
            $request->session()->put('url.intended', $request->fullUrl());
        } else {
            $request->session()->put('url.intended', url()->previous());
        }

        return redirect()->route('login')
            ->with('error', 'Vui lòng đăng nhập để sử dụng giỏ hàng.');
    }
}
