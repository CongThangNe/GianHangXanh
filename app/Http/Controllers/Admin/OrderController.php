<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng
     */
    public function index()
    {
        $orders = Order::with('items')->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Hiển thị chi tiết một đơn hàng
     */
    public function show($id)
    {
        $order = Order::with(['items.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Xóa đơn hàng (tùy chọn)
     * — hiện chưa có trong code gốc, nhưng thêm để tương thích với Route::resource()
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Đã xóa đơn hàng thành công!');
    }
}
