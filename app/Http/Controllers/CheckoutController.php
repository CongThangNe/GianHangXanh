<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use App\Http\Controllers\PaymentController;

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
        // [SỬA MỚI 1]: Thêm 'vnpay' vào rule validation
        $request->validate([
            'payment_method'   => 'required|in:cod,zalopay,vnpay',
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
            'note'             => 'nullable|string',
        ]);

        // ... (Giữ nguyên logic cập nhật user phone) ...
        if ($user = $request->user()) {
            if (empty($user->phone) || $user->phone !== $request->customer_phone) {
                $user->phone = $request->customer_phone;
                $user->save();
            }
        }

        $sessionId = session()->getId();
        $cart = Cart::with(['items.variant'])
            ->where('session_id', $sessionId)
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return redirect('/cart')->with('error', 'Giỏ hàng trống!');
        }

        // ... (Giữ nguyên logic tính toán tiền hàng & giảm giá) ...
        $subtotal = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $discountAmount = 0;
        $discountCode = session('discount_code');
        // ... (Logic tính discount giữ nguyên) ...
        if ($discountCode) {
            $code = DiscountCode::where('code', $discountCode)->first(); // Rút gọn cho ngắn
            if ($code) {
                // ... logic tính toán discount ...
                // Giả sử logic cũ của bạn ở đây đúng
                if ($code->type === 'percent') {
                    $discountAmount = $subtotal * ($code->discount_percent / 100);
                    if ($code->max_discount_value) $discountAmount = min($discountAmount, $code->max_discount_value);
                } else {
                    $discountAmount = $code->discount_value;
                }
                $code->increment('used_count');
            }
        }

        $total = max(0, $subtotal - $discountAmount);

        // 4. TẠO ĐƠN HÀNG
        $order = Order::create([
            'order_code'       => 'DH' . now()->format('Ymd') . Str::upper(Str::random(4)),
            'total'            => $total,
            'payment_method'   => $request->payment_method,
            'status'           => 'pending', // Mặc định là chờ xử lý
            'customer_name'    => $request->customer_name,
            'customer_phone'   => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'note'             => $request->note,
        ]);

        // 5. LƯU CHI TIẾT + TRỪ KHO (Giữ nguyên)
        foreach ($cart->items as $item) {
            OrderDetail::create([
                'order_id'           => $order->id,
                'product_id'         => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'quantity'           => $item->quantity,
                'price'              => $item->price,
            ]);

            $variant = ProductVariant::find($item->product_variant_id);
            if ($variant) {
                $variant->stock = max(0, $variant->stock - $item->quantity);
                $variant->save();
            }
        }

        // 6. XÓA GIỎ HÀNG
        // $cart->items()->delete();
        // $cart->delete();
        // session()->forget('discount_code');
        if ($request->payment_method === 'cod') {
            $cart->items()->delete();
            $cart->delete();
            session()->forget('discount_code');
        }

        // --- PHÂN LOẠI XỬ LÝ THEO CỔNG THANH TOÁN ---

        // 1. Thanh toán COD
        if ($request->payment_method === 'cod') {
            return redirect()->route('home')
                ->with('success', "Đơn hàng #{$order->order_code} đã được đặt thành công!");
        }

        // [SỬA MỚI 2]: Xử lý VNPAY
        // Chuyển hướng sang PaymentController kèm theo số tiền và ID đơn hàng
        if ($request->payment_method === 'vnpay') {
            return redirect()->route('payment.create', [
                'amount'   => $total,       // Truyền số tiền cần thanh toán
                'order_id' => $order->id    // Truyền ID đơn hàng để VNPAY tham chiếu
            ]);
        }

        // 3. Thanh toán ZaloPay 
        return response()->json([
            'success'    => true,
            'order_id'   => $order->id,
            'order_code' => $order->order_code,
            'total'      => $total,
        ]);
    }
}
