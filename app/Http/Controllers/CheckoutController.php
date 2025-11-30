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

        // Lấy mã giảm giá từ session (nếu đã áp dụng)
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
                    'code'   => $code->code,
                    'type'   => $code->type,
                    'value'  => $code->type === 'percent'
                        ? $code->discount_percent . '%'
                        : number_format($code->discount_value) . 'đ',
                    'amount' => $discountAmount,
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
            'discountInfo',
            'discountCode',
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

        // 1. TÍNH SUBTOTAL (tiền hàng chưa giảm)
        $subtotal = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // 2. TÍNH GIẢM GIÁ (nếu có mã)
        $discountAmount = 0;
        $discountCode = session('discount_code');

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
                    // Giảm theo %
                    $discountAmount = $subtotal * ($code->discount_percent / 100);

                    if ($code->max_discount_value) {
                        $discountAmount = min($discountAmount, $code->max_discount_value);
                    }
                } else {
                    // Giảm theo số tiền cố định
                    $discountAmount = $code->discount_value;
                }

                // Tăng số lần sử dụng mã sau khi đơn được tạo
                $code->increment('used_count');
            } else {
                // Mã không còn hợp lệ thì bỏ luôn trong session
                session()->forget('discount_code');
            }
        }

        // 3. TỔNG TIỀN CUỐI CÙNG SAU GIẢM
        $total = max(0, $subtotal - $discountAmount);

        // 4. TẠO ĐƠN HÀNG (LƯU TỔNG ĐÃ GIẢM)
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

        // 5. LƯU CHI TIẾT ĐƠN HÀNG + TRỪ KHO
        foreach ($cart->items as $item) {
            OrderDetail::create([
                'order_id'           => $order->id,
                'product_id'         => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'quantity'           => $item->quantity,
                'price'              => $item->price,
            ]);

            // GIẢM TỒN KHO
            $variant = ProductVariant::find($item->product_variant_id);

            if ($variant) {
                $variant->stock = max(0, $variant->stock - $item->quantity);
                $variant->save();
            }
        }

        // 6. XÓA GIỎ HÀNG VÀ MÃ GIẢM SAU KHI ĐẶT
        $cart->items()->delete();
        $cart->delete();
        session()->forget('discount_code');

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
