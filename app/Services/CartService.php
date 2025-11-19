<?php
namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getCart()
    {
        $sessionId = session()->getId();
        return Cart::with(['items.variant.product'])
            ->firstOrCreate(
                ['session_id'=>$sessionId],
                ['user_id'=>Auth::id()]
            );
    }

    public function addToCart($variantId, $quantity)
    {
        $variant = ProductVariant::findOrFail($variantId);
        if($variant->stock <=0){
            throw new \Exception('Biến thể này đã hết hàng.');
        }
        $cart = $this->getCart();
        $item = $cart->items()->where('product_variant_id',$variantId)->first();
        $newQty = $quantity;
        if($item){
            $newQty = $item->quantity + $quantity;
        }
        if($newQty > $variant->stock){
            throw new \Exception('Số lượng vượt quá tồn kho (tối đa '.$variant->stock.').');
        }
        if($item){
            $item->update(['quantity'=>$newQty,'price'=>$variant->price]);
        } else {
            $cart->items()->create([
                'product_variant_id'=>$variantId,
                'quantity'=>$quantity,
                'price'=>$variant->price
            ]);
        }
        return $cart->fresh(['items.variant.product']);
    }

    public function updateItem($itemId,$quantity)
    {
        $item=CartItem::findOrFail($itemId);
        $variant=$item->variant;
        if($quantity > $variant->stock){
            throw new \Exception('Số lượng vượt quá tồn kho (tối đa '.$variant->stock.').');
        }
        $item->update(['quantity'=>$quantity,'price'=>$variant->price]);
        return $item->cart->fresh(['items.variant.product']);
    }

    public function removeItem($itemId)
    {
        $item=CartItem::findOrFail($itemId);
        $cart=$item->cart;
        $item->delete();
        return $cart->fresh(['items.variant.product']);
    }
}
