<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $sessionId = session()->getId();
        $cart = Cart::with(['items.variant.product'])->where('session_id', $sessionId)->first();
        $cartItems = $cart?->items ?? collect([]);
        $total = $cartItems->sum(fn($i) => $i->price * $i->quantity);

        return view('checkout.index', compact('cartItems', 'total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method'   => 'required|in:cod,zalopay',
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
            'note'             => 'nullable|string',
        ]);

        $sessionId = session()->getId();
        $cart = Cart::with('items.variant')->where('session_id', $sessionId)->firstOrFail();

        if ($cart->items->isEmpty()) {
            return back()->with('error', 'Giỏ hàng trống!');
        }

        $total = $cart->items->sum(fn($i) => $i->price * $i->quantity);

        // Tạo đơn hàng trước (dù COD hay ZaloPay)
        $order = Order::create([
            'order_code'       => 'DH' . now()->format('Ymd') . Str::upper(Str::random(4)),
            'total'            => $total,
            'payment_method'   => $request->payment_method,
            'status'           => $request->payment_method === 'cod' ? 'paid' : 'pending',
            'customer_name'    => $request->customer_name,
            'customer_phone'   => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'note'             => $request->note,
        ]);

        // Lưu chi tiết đơn hàng
        foreach ($cart->items as $item) {
            OrderDetail::create([
                'order_id'           => $order->id,
                'product_variant_id' => $item->variant_id,
                'quantity'           => $item->quantity,
                'price'              => $item->price,
            ]);
        }

        // XÓA GIỎ HÀNG SAU KHI ĐÃ TẠO ĐƠN
        $cart->items()->delete();
        $cart->delete();

        if ($request->payment_method === 'cod') {
            return redirect()->route('home')
                ->with('success', "Đơn hàng COD #{$order->order_code} đã được tạo thành công!");
        }

        // ZaloPay → trả về JSON để JS tạo QR thật
        return response()->json([
            'success'     => true,
            'order_id'    => $order->id,
            'order_code'  => $order->order_code,
            'total'       => $total,
        ]);
    }
}
