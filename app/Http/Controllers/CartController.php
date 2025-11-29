<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\DiscountCode;

class CartController extends Controller
{
    public function index()
    {
        $sessionId = session()->getId();

        $cart = Cart::with([
            'items.variant.product',
            'items.variant.values.attribute'
        ])->where('session_id', $sessionId)->first();

        $cartItems = $cart?->items ?? collect([]);
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
                    'code' => $code->code,
                    'type' => $code->type,
                    'value' => $code->type === 'percent' ? $code->discount_percent . '%' : number_format($code->discount_value) . 'đ',
                    'amount' => $discountAmount
                ];
            } else {
                // Mã không hợp lệ → xóa khỏi session
                session()->forget('discount_code');
                $discountCode = null;
            }
        }

        $total = $subtotal - $discountAmount;

        return view('cart.index', compact(
        'cart', 
        'cartItems',
        'subtotal', 
        'discountCode', 
        'discountInfo', 
        'discountAmount', 
        'total'
));
    }

    public function applyDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20'
        ]);

        $code = DiscountCode::where('code', strtoupper(trim($request->code)))
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->where(function ($q) {
                $q->whereNull('max_uses')->orWhereRaw('used_count < max_uses');
            })
            ->first();

        if (!$code) {
            return back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn!');
        }

        // Lưu vào session để dùng tiếp ở checkout
        session(['discount_code' => $code->code]);

        return back()->with('success', "Áp dụng mã giảm giá thành công! Giảm " . ($code->type === 'percent' ? $code->discount_percent . '%' : number_format($code->discount_value) . 'đ'));
    }

    public function removeDiscount()
    {
        session()->forget('discount_code');
        return back()->with('success', 'Đã bỏ áp dụng mã giảm giá');
    }

    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::with('product')->findOrFail($request->variant_id);
        $price = $variant->price ?? $variant->product->price;

        $sessionId = session()->getId();

        $cart = Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => null]
        );

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variant->id)
            ->first();

        if ($item) {
            $item->increment('quantity', $request->quantity);
        } else {
            CartItem::create([
                'cart_id'            => $cart->id,
                'product_id'         => $variant->product_id,
                'product_variant_id' => $variant->id,
                'quantity'           => $request->quantity,
                'price'              => $price,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng');
    }

    public function remove($id)
    {
        CartItem::destroy($id);
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
    }

    public function clear()
    {
        $sessionId = session()->getId();
        Cart::where('session_id', $sessionId)->delete();
        session()->forget('discount_code'); // Xóa luôn mã giảm nếu có
        return back()->with('success', 'Đã làm trống giỏ hàng');
    }

    // THÊM METHOD NÀY: Cập nhật số lượng realtime + tính lại tổng
    public function update(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::findOrFail($request->item_id);
        $sessionId = session()->getId();
        $cart = Cart::where('session_id', $sessionId)->first();

        if ($cart && $item->cart_id === $cart->id) {
            // Kiểm tra stock (nếu có)
            $variant = $item->variant;
            $maxStock = $variant ? $variant->stock : 999;
            if ($request->quantity > $maxStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vượt quá tồn kho!',
                    'current_quantity' => $item->quantity,
                ], 400);
            }

            $item->quantity = $request->quantity;
            $item->save();

            $line_total = $item->price * $item->quantity;

            // Tính lại subtotal toàn giỏ
            $subtotal = $cart->items->sum(fn($i) => $i->price * $i->quantity);

            // Tính lại discount nếu có (copy logic từ index)
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
                        $discountAmount = $subtotal * ($code->discount_percent / 100);
                        if ($code->max_discount_value) {
                            $discountAmount = min($discountAmount, $code->max_discount_value);
                        }
                    } else {
                        $discountAmount = $code->discount_value;
                    }
                }
            }

            $total = $subtotal - $discountAmount;

            return response()->json([
                'success' => true,
                'line_total' => $line_total,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total' => $total,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Lỗi cập nhật!'], 400);
    }
}