<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\DiscountCode;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $sessionId = session()->getId();

        $cart = Cart::with([
            'items.variant.product',
            'items.variant.values.attribute',
            'items.variant.product.variants.values.attribute'
        ])->where('session_id', $sessionId)->first();

        $cartItems = $cart?->items ?? collect();
        $subtotal  = $cartItems->sum(fn($i) => $i->price * $i->quantity);

        // ===== DISCOUNT (CHỈ TÍNH – KHÔNG TRỪ LƯỢT) =====
        $discountAmount = 0;
        $discountInfo   = null;
        $codeStr        = session('discount_code');

        if ($codeStr) {
            $code = DiscountCode::where('code', $codeStr)
                ->where('active', true)
                ->where('max_uses', '>', 0)
                ->whereColumn('used_count', '<', 'max_uses')
                ->where(function ($q) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
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

                $discountInfo = [
                    'code'       => $code->code,
                    'type'       => $code->type,
                    'value'      => $code->type === 'percent'
                        ? $code->value . '%'
                        : number_format($code->value, 0, ',', '.') . 'đ',
                    'amount'     => $discountAmount,
                    'used_count' => $code->used_count,
                    'max_uses'   => $code->max_uses,
                ];
            } else {
                session()->forget('discount_code');
            }
        }

        $total = max(0, $subtotal - $discountAmount);

        return view('cart.index', compact(
            'cart',
            'cartItems',
            'subtotal',
            'discountAmount',
            'discountInfo',
            'total'
        ));
    }

    public function applyDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $code = DiscountCode::where('code', strtoupper(trim($request->code)))
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

        if (!$code) {
            return back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết lượt');
        }

        session(['discount_code' => $code->code]);

        return back()->with('success', 'Áp dụng mã giảm giá thành công');
    }

    public function removeDiscount()
    {
        session()->forget('discount_code');
        return back()->with('success', 'Đã bỏ mã giảm giá');
    }


    // ================== ADD ==================
    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $sessionId = session()->getId();
        $variantId = (int) $request->variant_id;
        $qtyAdd    = (int) $request->quantity;

        try {
            DB::transaction(function () use ($sessionId, $variantId, $qtyAdd) {

                $cart = Cart::firstOrCreate(
                    ['session_id' => $sessionId],
                    ['user_id' => null]
                );

                $variant = ProductVariant::with('product')
                    ->where('id', $variantId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($variant->stock < $qtyAdd) {
                    throw new \RuntimeException('Số lượng vượt quá tồn kho');
                }

                $variant->decrement('stock', $qtyAdd);

                $price = $variant->price ?? $variant->product->price;

                $item = CartItem::where('cart_id', $cart->id)
                    ->where('product_variant_id', $variant->id)
                    ->lockForUpdate()
                    ->first();

                if ($item) {
                    $item->increment('quantity', $qtyAdd);
                } else {
                    CartItem::create([
                        'cart_id'            => $cart->id,
                        'product_id'         => $variant->product_id,
                        'product_variant_id' => $variant->id,
                        'quantity'           => $qtyAdd,
                        'price'              => $price,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng');
    }
    public function update(Request $request)
    {
        $request->validate([
            'item_id'  => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $sessionId = session()->getId();

        try {
            return DB::transaction(function () use ($request, $sessionId) {

                $cart = Cart::with('items')
                    ->where('session_id', $sessionId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $item = $cart->items()
                    ->where('id', $request->item_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $variant = ProductVariant::lockForUpdate()
                    ->findOrFail($item->product_variant_id);

                $oldQty = $item->quantity;
                $newQty = (int) $request->quantity;
                $diff   = $newQty - $oldQty;

                // kiểm tra tồn kho
                if ($diff > 0 && $variant->stock < $diff) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Số lượng vượt quá tồn kho',
                        'current_quantity' => $oldQty
                    ]);
                }

                // cập nhật tồn
                if ($diff > 0) {
                    $variant->decrement('stock', $diff);
                } elseif ($diff < 0) {
                    $variant->increment('stock', abs($diff));
                }

                $item->update(['quantity' => $newQty]);

                // ===== TÍNH LẠI GIỎ =====
                $subtotal = CartItem::where('cart_id', $cart->id)
                    ->sum(DB::raw('price * quantity'));

                // ===== DISCOUNT =====
                $discountAmount = 0;
                $codeStr = session('discount_code');

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

                    } else {
                        session()->forget('discount_code');
                    }
                }

                $total = max(0, $subtotal - $discountAmount);

                return response()->json([
                    'success'         => true,
                    'line_total'      => $item->price * $newQty,
                    'subtotal'        => $subtotal,
                    'discount_amount' => $discountAmount,
                    'total'           => $total,
                    'quantity'        => $newQty,
                    'max_allowed'     => $newQty + $variant->stock,
                ]);
            });
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật giỏ hàng'
            ], 500);
        }
    }

    // ================== REMOVE ==================
    public function remove($id)
    {
        $sessionId = session()->getId();

        DB::transaction(function () use ($sessionId, $id) {

            $cart = Cart::where('session_id', $sessionId)->lockForUpdate()->first();
            if (!$cart) return;

            $item = CartItem::where('id', $id)
                ->where('cart_id', $cart->id)
                ->lockForUpdate()
                ->first();

            if (!$item) return;

            $variant = ProductVariant::where('id', $item->product_variant_id)
                ->lockForUpdate()
                ->first();

            if ($variant) {
                $variant->increment('stock', $item->quantity);
            }

            $item->delete();
        });

        return back()->with('success', 'Đã xóa sản phẩm');
    }

    // ================== CLEAR ==================
    public function clear()
    {
        $sessionId = session()->getId();

        DB::transaction(function () use ($sessionId) {

            $cart = Cart::with('items')
                ->where('session_id', $sessionId)
                ->lockForUpdate()
                ->first();

            if (!$cart) return;

            foreach ($cart->items as $item) {
                ProductVariant::where('id', $item->product_variant_id)
                    ->lockForUpdate()
                    ->increment('stock', $item->quantity);
            }

            $cart->items()->delete();
            $cart->delete();
        });

        session()->forget('discount_code');

        return back()->with('success', 'Đã làm trống giỏ hàng');
    }
}
