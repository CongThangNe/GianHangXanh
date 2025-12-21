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
        $displayDiscount = 0; // Thêm biến mới để hiển thị giá trị gốc
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
                // Giá trị hiển thị giữ nguyên giá trị gốc đã tính
                $displayDiscount = $discountAmount;

                // Giới hạn giá trị áp dụng thực tế không vượt quá subtotal
                $discountAmount = min($discountAmount, $subtotal);

                $discountInfo = [
                    'code'   => $code->code,
                    'type'   => $code->type,
                    'value'  => $code->type === 'percent'
                        ? $code->discount_percent . '%'
                        : number_format($displayDiscount, 0, ',', '.') . 'đ',
                    'amount' => $discountAmount,
                    'display_amount' => $displayDiscount // Thêm để view dùng hiển thị
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
            'payment_method'   => 'required|in:cod,zalopay,vnpay',
            'customer_name'    => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[\p{L}\s]+$/u',
            ],
            'customer_phone'   => [
                'required',
                'digits_between:10,11',
                'starts_with:0,84' 
            ],
            'customer_address' => 'required|string|min:10|max:500',
            'customer_email'   => 'nullable|email|max:255',
            'note'             => 'nullable|string|max:1000',

        ], [
            // Thông báo lỗi
            'customer_name.required'     => 'Vui lòng nhập họ và tên.',
            'customer_name.min'          => 'Họ và tên phải có ít nhất :min ký tự.',
            'customer_name.regex'        => 'Họ và tên không được chứa số hoặc ký tự đặc biệt.',

            'customer_phone.required'    => 'Vui lòng nhập số điện thoại.',
            'customer_phone.digits_between' => 'Số điện thoại phải là chữ số và có độ dài từ :min đến :max ký tự.',

            'customer_address.required'  => 'Vui lòng nhập địa chỉ giao hàng.',
            'customer_address.min'       => 'Địa chỉ phải có ít nhất :min ký tự.',
            'customer_email.email'       => 'Email không đúng định dạng.',
        ]);

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

        $subtotal = $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $discountAmount = 0;
        $displayDiscount = 0; // Thêm biến mới để hiển thị giá trị gốc
        $discountCode = session('discount_code');
        if ($discountCode) {
            $code = DiscountCode::where('code', $discountCode)->first();
            if ($code) {
                if ($code->type === 'percent') {
                    $discountAmount = $subtotal * ($code->discount_percent / 100);
                    if ($code->max_discount_value) $discountAmount = min($discountAmount, $code->max_discount_value);
                } else {
                    $discountAmount = $code->discount_value;
                }
                // Giá trị hiển thị giữ nguyên giá trị gốc đã tính
                $displayDiscount = $discountAmount;

                // Giới hạn giá trị áp dụng thực tế không vượt quá subtotal
                $discountAmount = min($discountAmount, $subtotal);
                $code->increment('used_count');
            }
        }

        $total = max(0, $subtotal - $discountAmount);

        // 4. TẠO ĐƠN HÀNG
        $order = Order::create([
            'order_code'       => 'DH' . now()->format('Ymd') . Str::upper(Str::random(4)),
            'total'            => $total,
            'payment_method'   => $request->payment_method,
            'payment_status'   => 'unpaid',
            'delivery_status'   => 'pending', // Mặc định là chờ xử lý
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
