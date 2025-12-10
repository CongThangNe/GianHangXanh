<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderGuestController extends Controller
{
    public function cancel(Request $request, $order_code)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $order = Order::where('order_code', $order_code)
                      ->where('status', 'pending')
                      ->firstOrFail();

        // Kiểm tra số điện thoại trùng khớp
        if ($order->customer_phone !== $request->phone) {
            return back()->with('error', 'Số điện thoại không đúng. Không thể hủy đơn hàng!');
        }

        // XÓA HOÀN TOÀN đơn hàng + chi tiết
        $order->details()->delete();
        $order->delete();

        return redirect()->route('user.orders.index')
                         ->with('success', 'Đơn hàng #'.$order_code.' đã được hủy thành công!');
    }
}