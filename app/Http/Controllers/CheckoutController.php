<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth; // ✔ Dùng Auth facade

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        // Validate phương thức thanh toán
        $data = $request->validate([
            'payment_method' => 'required|in:cod,online',
        ]);

        $paymentMethod = $data['payment_method'];

        // Lấy giỏ hàng hiện tại theo session
        $sessionId = session()->getId();
        $cart = Cart::where('session_id', $sessionId)
                    ->with('items.variant.product')
                    ->first();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')
                ->withErrors(['cart' => 'Không có sản phẩm trong giỏ hàng để thanh toán.']);
        }

        // Tính tổng tiền
        $total = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Tạo đơn hàng
        $order = \App\Models\Order::create([
            'user_id'        => Auth::id(), // ✔ Dùng Auth::id()
            'total_price'    => $total,
            'status'         => $paymentMethod === 'online' ? 'paid' : 'pending',
            'payment_method' => $paymentMethod,
        ]);

        // Tạo chi tiết đơn hàng
        foreach ($cart->items as $item) {
            \App\Models\OrderDetail::create([
                'order_id'   => $order->id,
                'product_id' => $item->variant->product->id ?? null,
                'quantity'   => $item->quantity,
                'price'      => $item->price,
            ]);
        }

        // Xóa giỏ hàng
        $cart->items()->delete();
        $cart->delete();

        // Chuyển đến trang xác nhận đơn hàng
        return redirect()->route('order.confirmation', ['id' => $order->id]);
    }
}
