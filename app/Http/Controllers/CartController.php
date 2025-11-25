<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;

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
    $total = $cartItems->sum(fn($i) => $i->price * $i->quantity);

    return view('cart.index', compact('cart', 'cartItems', 'total'));
}


    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::with('product')->findOrFail($request->variant_id);

        // GIÁ PHẢI LẤY TỪ BIẾN THỂ — CHUẨN NHẤT
        $price = $variant->price;

        $sessionId = session()->getId();

        $cart = Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => null]
        );

        // Nếu sản phẩm + biến thể đã có → tăng số lượng
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variant->id)
            ->first();

        if ($item) {
            $item->update([
                'quantity' => $item->quantity + $request->quantity
            ]);
        } else {
            CartItem::create([
                'cart_id'          => $cart->id,
                'product_id'       => $variant->product_id,
                'product_variant_id' => $variant->id,
                'quantity'         => $request->quantity,
                'price'            => $price,     // QUAN TRỌNG
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ');
    }

    public function remove($id)
    {
        CartItem::destroy($id);
        return back()->with('success', 'Đã xoá');
    }

    public function clear()
    {
        $sessionId = session()->getId();
        Cart::where('session_id', $sessionId)->delete();
        return back()->with('success', 'Xoá toàn bộ giỏ hàng');
    }
}
