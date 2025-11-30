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
     * - Load sẵn quan hệ cần thiết để tránh N+1.
     */
    public function index(Request $request)
    {
        $query = Order::query()
            ->withCount('details')
            ->orderByDesc('created_at');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Xem chi tiết đơn hàng (Admin).
     */
    public function show($id)
    {
        $order = Order::with([
            'details.variant.product',
            'details.variant.values.attribute',
        ])->findOrFail($id);

        // Tính tiền hàng (subtotal) từ chi tiết đơn
        $subtotal = $this->calculateTotal($order);

        // Giảm giá = chênh lệch giữa tiền hàng và tổng thanh toán thực tế
        $discountAmount = max(0, (int) $subtotal - (int) $order->total);

        return view('admin.orders.show', compact('order', 'subtotal', 'discountAmount'));
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
        $validated = $request->validate([
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
