<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\OrderDetail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $sessionId = session()->getId();

        $cart = Cart::with([
            'items.variant.product',
            'items.variant.values.attribute'
        ])->where('session_id', $sessionId)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
        }

        $cartItems = $cart->items;
        $subtotal = $cartItems->sum(fn($i) => $i->price * $i->quantity);

        $discountCode = session('discount_code');
        $discountAmount = 0;
        $discountInfo = null;

        if ($discountCode) {
            $code = DiscountCode::where('code', $discountCode)
                ->where('starts_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->where(function ($q) {
                    $q->whereNull('max_uses')->orWhereRaw('used_count < max_uses');
                })
                ->first();

            if ($code) {
                if ($code->type === 'percent') {
                    $discountAmount = $subtotal * ($code->discount_percent / 100);
                    if ($code->max_discount_value) {
                        $discountAmount = min($discountAmount, $code->max_discount_value);
                    }
                } else {
                    $discountAmount = $code->discount_value;
                }

                $discountInfo = [
                    'code' => $code->code,
                    'type' => $code->type,
                    'value' => $code->type === 'percent' ? $code->discount_percent . '%' : number_format($code->discount_value) . 'đ',
                    'amount' => $discountAmount
                ];
            } else {
                session()->forget('discount_code');
            }
        }

        $total = $subtotal - $discountAmount;

        return view('checkout.index', compact(
            'cartItems',
            'subtotal',
            'discountAmount',
            'total'
        ));
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

        $cart = Cart::with(['items.variant'])
            ->where('session_id', $sessionId)
            ->firstOrFail();

        // Nếu giỏ hàng trống
        if ($cart->items->isEmpty()) {
            return redirect('/cart')->with('error', 'Giỏ hàng trống!');
        }

        // TÍNH TỔNG TIỀN
        $total = $cart->items->sum(fn($i) => $i->price * $i->quantity);

        // TẠO ĐƠN HÀNG
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

        // LƯU CHI TIẾT ĐƠN HÀNG
        foreach ($cart->items as $item) {

            OrderDetail::create([
                'order_id'          => $order->id,
                'product_id'        => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'quantity'          => $item->quantity,
                'price'             => $item->price,
            ]);

            // GIẢM TỒN KHO
            $variant = ProductVariant::find($item->product_variant_id);

            if ($variant) {
                $variant->stock = max(0, $variant->stock - $item->quantity);
                $variant->save();
            }
        }




        // XÓA GIỎ HÀNG SAU KHI ĐẶT
        $cart->items()->delete();
        $cart->delete();

        // COD → redirect về home
        if ($request->payment_method === 'cod') {
            return redirect()->route('home')
                ->with('success', "Đơn hàng COD #{$order->order_code} đã được tạo thành công!");
        }

        // ZaloPay → trả JSON cho JS xử lý QR
        return response()->json([
            'success'     => true,
            'order_id'    => $order->id,
            'order_code'  => $order->order_code,
            'total'       => $total,
        ]);
    }
}
