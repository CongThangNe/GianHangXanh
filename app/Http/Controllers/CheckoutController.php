<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth; // ✔ Dùng Auth facade

class CheckoutController extends Controller
{
    public function index()
    {
        $sessionId = session()->getId();
        $cart = Cart::with(['items.variant.product'])
            ->where('session_id', $sessionId)
            ->first();

        $cartItems = $cart ? $cart->items : collect([]);
        $total = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return view('checkout.index', compact('cartItems', 'total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cod,online'
        ]);

        $sessionId = session()->getId();
        $cart = Cart::with('items')
            ->where('session_id', $sessionId)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return back()->with('error', 'Giỏ hàng trống!');
        }

        // Tính tổng tiền
        $total = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        if ($request->payment_method === 'cod') {
            // Xử lý COD: Xóa giỏ hàng
            $cart->items()->delete();
            $cart->delete();

            return redirect()->route('home')->with('success', 'Đơn hàng COD đã được tạo thành công! Tổng: ' . number_format($total) . '₫');
        }

        if ($request->payment_method === 'online') {
            // Lưu tạm để thanh toán ZaloPay
            session(['pending_cart' => $cart->toArray()]);
            return redirect()->route('payment.zalopay');
        }

        return back()->with('error', 'Phương thức không hợp lệ.');
    }
}
