<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;

class CartController extends Controller
{
    /**
     * Hiển thị giỏ hàng hiện tại.
     */
    public function index()
{
    $sessionId = session()->getId();

    $cart = Cart::with(['items.variant.product'])
        ->where('session_id', $sessionId)
        ->first();

    // FIX: Nếu không có cart, tạo object giả để tránh null (nhưng items rỗng)
    if (!$cart) {
        $cart = new \stdClass();
        $cart->items = collect([]);
    }

    return view('cart.index', compact('cart'));
}

    /**
     * Thêm sản phẩm (biến thể) vào giỏ.
     */
    public function add(Request $request)
    {
        $data = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::findOrFail($data['variant_id']);

        // Kiểm tra tồn kho biến thể
        if ($variant->stock <= 0) {
            return $this->errorResponse($request, 'Biến thể này đã hết hàng.');
        }

        $sessionId = session()->getId();

        // Lấy hoặc tạo cart theo session hiện tại
        $cart = Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => Auth::id()]
        );

        // Tìm item cùng biến thể trong giỏ
        $cartItem = $cart->items()->where('product_variant_id', $variant->id)->first();

        $newQty = $data['quantity'];
        if ($cartItem) {
            $newQty = $cartItem->quantity + $data['quantity'];
        }

        // Không cho đặt quá tồn kho
        if ($newQty > $variant->stock) {
            return $this->errorResponse(
                $request,
                'Số lượng vượt quá tồn kho (tối đa ' . $variant->stock . ').'
            );
        }

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQty,
                'price'    => $variant->price,
            ]);
        } else {
            $cartItem = $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity'           => $newQty,
                'price'              => $variant->price,
            ]);
        }

        if ($request->wantsJson()) {
            $cart->load('items.variant.product');

            return response()->json([
                'status'  => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
                'cart'    => $cart,
            ]);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    /**
     * Cập nhật số lượng một item trong giỏ.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'item_id'  => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $sessionId = session()->getId();

        $cart = Cart::where('session_id', $sessionId)
            ->with('items.variant')
            ->firstOrFail();

        $item = $cart->items->where('id', $data['item_id'])->first();

        if (!$item) {
            return $this->errorResponse($request, 'Không tìm thấy sản phẩm trong giỏ.');
        }

        $variant = $item->variant;

        if ($data['quantity'] > $variant->stock) {
            return $this->errorResponse(
                $request,
                'Số lượng vượt quá tồn kho (tối đa ' . $variant->stock . ').'
            );
        }

        $item->update(['quantity' => $data['quantity']]);

        if ($request->wantsJson()) {
            $cart->load('items.variant.product');
            return response()->json([
                'status'  => true,
                'message' => 'Đã cập nhật số lượng.',
                'cart'    => $cart,
            ]);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã cập nhật số lượng sản phẩm.');
    }

    /**
     * Xoá một item khỏi giỏ.
     */
    public function remove(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:cart_items,id',
        ]);

        $sessionId = session()->getId();

        $cart = Cart::where('session_id', $sessionId)
            ->with('items')
            ->first();

        if (!$cart) {
            return $this->errorResponse($request, 'Giỏ hàng không tồn tại.');
        }

        $item = $cart->items->where('id', $data['item_id'])->first();

        if (!$item) {
            return $this->errorResponse($request, 'Không tìm thấy sản phẩm trong giỏ.');
        }

        $item->delete();

        if ($request->wantsJson()) {
            $cart->load('items.variant.product');

            return response()->json([
                'status'  => true,
                'message' => 'Đã xoá sản phẩm khỏi giỏ.',
                'cart'    => $cart,
            ]);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Đã xoá sản phẩm khỏi giỏ hàng.');
    }

    /**
     * Helper trả về lỗi cho cả web và API JSON.
     */
    protected function errorResponse(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json([
                'status'  => false,
                'message' => $message,
            ], 400);
        }

        return redirect()
            ->back()
            ->withErrors(['cart' => $message]);
    }
}
