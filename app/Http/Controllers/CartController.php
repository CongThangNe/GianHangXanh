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

        $cartItems = $cart?->items ?? collect([]);
        $subtotal = $cartItems->sum(fn($i) => $i->price * $i->quantity);

        $discountCode = session('discount_code');
        $discountAmount = 0;
        $displayDiscount = 0;
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

                $displayDiscount = $discountAmount;
                $discountAmount = min($discountAmount, $subtotal);

                $discountInfo = [
                    'code' => $code->code,
                    'type' => $code->type,
                    'value' => $code->type === 'percent'
                        ? $code->discount_percent . '%'
                        : number_format($displayDiscount, 0, ',', '.') . 'đ',
                    'amount' => $discountAmount,
                    'display_amount' => $displayDiscount
                ];
            } else {
                session()->forget('discount_code');
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

        session(['discount_code' => $code->code]);

        return back()->with('success', "Áp dụng mã giảm giá thành công! Giảm " .
            ($code->type === 'percent'
                ? $code->discount_percent . '%'
                : number_format($code->discount_value, 0, ',', '.') . 'đ'));
    }

    public function removeDiscount()
    {
        session()->forget('discount_code');
        return back()->with('success', 'Đã bỏ áp dụng mã giảm giá');
    }

    // ADD: thêm giỏ => TRỪ KHO NGAY
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

                if ((int)$variant->stock < $qtyAdd) {
                    throw new \RuntimeException('Số lượng không được vượt quá tồn kho');
                }

                // reserve stock
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

    // REMOVE ITEM: hoàn kho
    public function remove($id)
    {
        $sessionId = session()->getId();

        try {
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
                    $variant->increment('stock', (int)$item->quantity);
                }

                $item->delete();
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
    }

    // CLEAR: hoàn kho toàn bộ
    public function clear()
    {
        $sessionId = session()->getId();

        try {
            DB::transaction(function () use ($sessionId) {

                $cart = Cart::with('items')
                    ->where('session_id', $sessionId)
                    ->lockForUpdate()
                    ->first();

                if (!$cart) return;

                foreach ($cart->items as $item) {
                    $variant = ProductVariant::where('id', $item->product_variant_id)
                        ->lockForUpdate()
                        ->first();

                    if ($variant) {
                        $variant->increment('stock', (int)$item->quantity);
                    }
                }

                $cart->items()->delete();
                $cart->delete();
            });

            session()->forget('discount_code');

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Đã làm trống giỏ hàng');
    }

    // UPDATE QTY: trừ/bù kho theo DIFF + trả max_allowed cho UI
    public function update(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $sessionId = session()->getId();

        try {
            $payload = DB::transaction(function () use ($request, $sessionId) {

                $cart = Cart::where('session_id', $sessionId)->lockForUpdate()->first();
                if (!$cart) {
                    throw new \RuntimeException('Giỏ hàng không tồn tại!');
                }

                $item = CartItem::where('id', $request->item_id)
                    ->where('cart_id', $cart->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $variant = ProductVariant::where('id', $item->product_variant_id)
                    ->lockForUpdate()
                    ->first();

                $oldQty = (int) $item->quantity;
                $newQty = (int) $request->quantity;
                $diff   = $newQty - $oldQty;

                if ($variant) {
                    if ($diff > 0) {
                        if ((int)$variant->stock < $diff) {
                            throw new \RuntimeException('Số lượng không được vượt quá tồn kho');
                        }
                        $variant->decrement('stock', $diff);
                    } elseif ($diff < 0) {
                        $variant->increment('stock', abs($diff));
                    }
                }

                $item->quantity = $newQty;
                $item->save();

                // reload
                $cart->load('items');

                $line_total = $item->price * $item->quantity;
                $subtotal = $cart->items->sum(fn($i) => $i->price * $i->quantity);

                // discount
                $discountAmount = 0;
                $displayDiscount = 0;
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

                        $displayDiscount = $discountAmount;
                        $discountAmount = min($discountAmount, $subtotal);
                    }
                }

                $total = $subtotal - $discountAmount;

                $remainingStock = (int)($variant?->stock ?? 0);
                $maxAllowed = (int)$item->quantity + $remainingStock;

                return [
                    'success' => true,
                    'line_total' => $line_total,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'display_discount_amount' => $displayDiscount,
                    'total' => $total,

                    // QUAN TRỌNG CHO UI
                    'remaining_stock' => $remainingStock,
                    'max_allowed' => $maxAllowed,
                    'current_quantity' => (int)$item->quantity,
                ];
            });

            return response()->json($payload);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // UPDATE VARIANT: hoàn kho cũ + giữ kho mới + trả max_allowed
    public function updateVariant(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
            'variant_id' => 'required|exists:product_variants,id',
        ]);

        $sessionId = session()->getId();

        try {
            $payload = DB::transaction(function () use ($request, $sessionId) {

                $cart = Cart::where('session_id', $sessionId)->lockForUpdate()->first();
                if (!$cart) {
                    throw new \RuntimeException('Giỏ hàng không tồn tại!');
                }

                $item = CartItem::with(['variant.product', 'cart.items'])
                    ->where('id', $request->item_id)
                    ->where('cart_id', $cart->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $oldVariant = ProductVariant::where('id', $item->product_variant_id)
                    ->lockForUpdate()
                    ->first();

                $newVariant = ProductVariant::with(['product', 'values'])
                    ->where('id', $request->variant_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ((int)$newVariant->product_id !== (int)$item->product_id) {
                    throw new \RuntimeException('Biến thể không hợp lệ cho sản phẩm này!');
                }

                $oldQty = (int)$item->quantity;

                // hoàn kho biến thể cũ
                if ($oldVariant) {
                    $oldVariant->increment('stock', $oldQty);
                }

                // qty mới không vượt tồn mới
                $availableNew = (int)($newVariant->stock ?? 0);
                $newQty = min($oldQty, max(1, $availableNew));

                if ($newQty > (int)$newVariant->stock) {
                    throw new \RuntimeException('Số lượng không được vượt quá tồn kho');
                }

                // giữ kho biến thể mới
                $newVariant->decrement('stock', $newQty);

                // gộp nếu đã có item cùng variant mới
                $existing = CartItem::where('cart_id', $cart->id)
                    ->where('product_variant_id', $newVariant->id)
                    ->where('id', '!=', $item->id)
                    ->lockForUpdate()
                    ->first();

                $newUnitPrice = (int)($newVariant->price ?? ($newVariant->product->price ?? 0));

                if ($existing) {
                    $existing->quantity = $existing->quantity + $newQty;
                    $existing->price = $newUnitPrice;
                    $existing->save();

                    $item->delete();
                    $item = $existing;
                } else {
                    $item->product_variant_id = $newVariant->id;
                    $item->price = $newUnitPrice;
                    $item->quantity = $newQty;
                    $item->save();
                }

                // totals
                $cart->load('items');
                $subtotal = $cart->items->sum(fn($i) => $i->price * $i->quantity);

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
                        $discountAmount = min($discountAmount, $subtotal);
                    }
                }

                $total = $subtotal - $discountAmount;

                $remainingStock = (int)($newVariant->stock ?? 0);
                $maxAllowed = (int)$item->quantity + $remainingStock;

                return [
                    'success' => true,
                    'item_id' => $item->id,
                    'unit_price' => (int)$item->price,
                    'quantity' => (int)$item->quantity,

                    // UI dùng cái này để chặn
                    'max_allowed' => $maxAllowed,
                    'remaining_stock' => $remainingStock,

                    'max_stock' => $maxAllowed,

                    'variant_label' => $newVariant->variant_label,
                    'line_total' => (int)($item->price * $item->quantity),
                    'subtotal' => (int)$subtotal,
                    'discount_amount' => (int)$discountAmount,
                    'total' => (int)$total,
                ];
            });

            return response()->json($payload);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
