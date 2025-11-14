<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\DiscountCode;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'details.product'])->latest()->get();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'discount_code' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'shipping_address' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal += $product->price * $item['quantity'];
            }

            $discountAmount = 0;
            $discountCodeId = null;

            if (!empty($validated['discount_code'])) {
                $code = DiscountCode::where('code', $validated['discount_code'])
                    ->where('expires_at', '>', now())
                    ->first();

                if ($code) {
                    $discountCodeId = $code->id;
                    if ($code->type === 'percent') {
                        $discountAmount = $subtotal * ($code->discount_percent / 100);
                    } else {
                        $discountAmount = $code->discount_value;
                    }
                    $discountAmount = min($discountAmount, $code->max_discount_value ?? $discountAmount);
                }
            }

            $order = Order::create([
                'user_id' => $validated['user_id'],
                'discount_code_id' => $discountCodeId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_price' => $subtotal - $discountAmount,
                'status' => 'pending',
                'shipping_address' => $validated['shipping_address'] ?? null,
                'payment_method' => $validated['payment_method'] ?? 'COD',
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            }

            return response()->json([
                'message' => 'Order created successfully!',
                'order' => $order->load('details.product'),
            ]);
        });
    }

    public function show($id)
    {
        $order = Order::with(['user', 'details.product', 'discountCode'])->findOrFail($id);
        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);
        return response()->json(['message' => 'Order status updated!', 'order' => $order]);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['message' => 'Order deleted!']);
    }
}
