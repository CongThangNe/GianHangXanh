<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;

class OrderController extends Controller
{
    /**
     * Danh sách đơn hàng (Admin).
     *
     * - Hiển thị bảng đơn hàng, trạng thái, tổng tiền, ngày tạo.
     * - Hỗ trợ lọc theo trạng thái (?status=pending|confirmed|...).
     * - Load sẵn quan hệ chi tiết để có thể dùng ngay khi cần.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = Order::with('details.variant.product')
            ->orderByDesc('id');

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(20);

        return view('admin.orders.index', compact('orders', 'status'));
    }

    /**
     * Xem chi tiết một đơn hàng.
     *
     * Load đầy đủ dữ liệu:
     * - Sản phẩm
     * - Biến thể
     * - Thuộc tính của biến thể
     * Đồng thời tính tổng tiền đơn dựa trên chi tiết.
     */
    public function show($id)
    {
        $order = Order::with([
            'details.variant.product',
            'details.variant.values.attribute',
        ])->findOrFail($id);

        // Tính tổng tiền đơn (phục vụ hiển thị / kiểm tra nếu cần).
        $order->calculated_total = $this->calculateTotal($order);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng (Admin).
     *
     * Xử lý trực tiếp trong controller theo đúng yêu cầu:
     * - Các trạng thái hỗ trợ: pending, confirmed, preparing,
     *   shipping, delivered, cancelled.
     * - Nếu gửi giá trị khác sẽ bị validate lỗi.
     *
     * Route đang dùng: PATCH /admin/orders/{order}/status
     * name: admin.orders.updateStatus
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,preparing,shipping,delivered,cancelled',
        ]);

        $order->status = $request->input('status');
        $order->save();

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    /**
     * Tính tổng tiền của một đơn hàng dựa trên bảng order_details.
     * Đây là phần "tính tổng tiền đơn" cho Huy, dùng nội bộ trong controller.
     */
    protected function calculateTotal(Order $order): int
    {
        // Nếu chưa load quan hệ thì load thêm.
        if (! $order->relationLoaded('details')) {
            $order->load('details');
        }

        return $order->details->sum(function (OrderDetail $detail) {
            return (int) $detail->price * (int) $detail->quantity;
        });
    }
}
