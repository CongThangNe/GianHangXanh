<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        // Lấy giỏ hàng theo session hiện tại
        $sessionId = session()->getId();

        $cart = Cart::where('session_id', $sessionId)
                    ->with('items')
                    ->first();

        // Middleware đã check giỏ hàng, nhưng kiểm tra lại để an toàn
        if (!$cart || $cart->items->count() === 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Không có sản phẩm trong giỏ hàng để thanh toán.',
            ], 400);
        }

        // --- TÍNH TỔNG TIỀN ---
        $total = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // --- TẠO ĐƠN HÀNG (tùy bạn có muốn tạo model orders không) ---
        // Ví dụ đơn giản:
        // $order = Order::create([...]);

        // --- SAU KHI THANH TOÁN XONG có thể xoá giỏ ---
        // $cart->items()->delete();
        // $cart->delete();

        return response()->json([
            'status'      => true,
            'message'     => 'Thanh toán thành công.',
            'total_price' => $total,
            'cart_items'  => $cart->items,
        ]);
    }
}
