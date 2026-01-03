<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * HIỂN THỊ CHECKOUT
     * - Preview đơn hàng
     * - Áp mã giảm giá từ session
     */
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
        $subtotal  = $cartItems->sum(fn ($i) => $i->price * $i->quantity);

        $discountAmount = 0;
        $discountInfo   = null;
        $codeStr        = session('discount_code');

        if ($codeStr) {
            $code = DiscountCode::where('code', $codeStr)
                ->where('active', true)
                ->where(function ($q) {
                    $q->where('max_uses', 0)
                      ->orWhereColumn('used_count', '<', 'max_uses');
                })
                ->where(function ($q) {
                    $q->whereNull('starts_at')
                      ->orWhere('starts_at', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', now());
                })
                ->first();

            if ($code) {
                if ($code->type === 'percent') {
                    $discountAmount = $subtotal * ($code->value / 100);

                    if ($code->max_discount_value > 0) {
                        $discountAmount = min($discountAmount, $code->max_discount_value);
                    }
                } else {
                    $discountAmount = $code->value;
                }

                $discountAmount = min($discountAmount, $subtotal);

                $discountInfo = [
                    'code'   => $code->code,
                    'type'   => $code->type,
                    'value'  => $code->type === 'percent'
                        ? $code->value . '%'
                        : number_format($code->value, 0, ',', '.') . 'đ',
                    'amount' => $discountAmount,
                ];
            } else {
                session()->forget('discount_code');
            }
        }

        $total = max(0, $subtotal - $discountAmount);

        return view('checkout.index', compact(
            'cartItems',
            'subtotal',
            'discountAmount',
            'discountInfo',
            'total'
        ));
    }

    /**
     * XỬ LÝ ĐẶT HÀNG
     * - Lock cart + discount
     * - Trừ lượt dùng
     * - Tạo order + order detail
     */
    public function process(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|min:3|max:100',
            'customer_phone'   => 'required|digits_between:10,11',
            'customer_address' => 'required|string|min:10',
            'payment_method'   => 'required|in:cod,vnpay',
            'note'             => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($request) {

            $sessionId = session()->getId();

            $cart = Cart::with('items')
                ->where('session_id', $sessionId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($cart->items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống!');
            }

            $subtotal = $cart->items->sum(fn ($i) => $i->price * $i->quantity);
            $discountAmount = 0;

            $discountCodeStr = session('discount_code');
            $discountCode    = null;

            if ($discountCodeStr) {
                $discountCode = DiscountCode::where('code', $discountCodeStr)
                    ->where('active', true)
                    ->where(function ($q) {
                        $q->where('max_uses', 0)
                          ->orWhereColumn('used_count', '<', 'max_uses');
                    })
                    ->where(function ($q) {
                        $q->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                    })
                    ->lockForUpdate()
                    ->first();

                if (!$discountCode) {
                    session()->forget('discount_code');
                }
            }

            if ($discountCode) {
                if ($discountCode->type === 'percent') {
                    $discountAmount = $subtotal * ($discountCode->value / 100);

                    if ($discountCode->max_discount_value > 0) {
                        $discountAmount = min($discountAmount, $discountCode->max_discount_value);
                    }
                } else {
                    $discountAmount = $discountCode->value;
                }

                $discountAmount = min($discountAmount, $subtotal);
                $discountCode->increment('used_count');
            }

            $total = max(0, $subtotal - $discountAmount);

            $order = Order::create([
                'order_code'       => 'DH' . now()->format('Ymd') . Str::upper(Str::random(4)),
                'total'            => $total,
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'unpaid',
                'delivery_status'  => 'pending',
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'note'             => $request->note,
            ]);

            foreach ($cart->items as $item) {
                OrderDetail::create([
                    'order_id'           => $order->id,
                    'product_id'         => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity'           => $item->quantity,
                    'price'              => $item->price,
                ]);
            }

            if ($request->payment_method === 'cod') {
                $cart->items()->delete();
                $cart->delete();
                session()->forget('discount_code');

                return redirect()->route('home')
                    ->with('success', "Đặt hàng thành công #{$order->order_code}");
            }

            if ($request->payment_method === 'vnpay') {
                return redirect()->route('payment.create', [
                    'order_id' => $order->id,
                    'amount'   => $total,
                ]);
            }

            abort(400);
        });
    }
}
