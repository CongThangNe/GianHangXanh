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
            $query->where('delivery_status', $status);
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
        // Nếu đơn đã giao thành công hoặc đã hủy thì không cho sửa nữa
        if (in_array($order->delivery_status, ['delivered', 'cancelled'], true)) {
            return back()->with('error', 'Đơn hàng đã hoàn tất, không thể thay đổi trạng thái nữa!');
        }

        $validated = $request->validate([
            'delivery_status' => 'required|string|in:pending,confirmed,preparing,shipping,delivered,cancelled',
            'payment_status'  => 'nullable|string|in:unpaid,paid',
        ]);

        $newDelivery = $request->input('delivery_status');

        $order->delivery_status = $newDelivery;

        // Quy ước: khi đơn đã giao thành công -> tự động chuyển sang "Đã thanh toán"
        // và sau khi lưu thì trạng thái sẽ bị khóa (đã xử lý ở đầu hàm cho các lần sửa tiếp theo).
        if ($newDelivery === 'delivered') {
            $order->payment_status = 'paid';
        } elseif ($request->filled('payment_status')) {
            $order->payment_status = $request->input('payment_status');
        }
        $order->save();

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }


    /**
     * Danh sách đơn hàng của user đang đăng nhập.
     * Lọc theo số điện thoại trong hồ sơ user (users.phone = orders.customer_phone).
     */
    public function userIndex(Request $request)
    {
        $user = $request->user();
        $statusFilter = $request->get('status', 'all');

        $query = Order::query()
            ->with(['details.variant.product'])
            ->orderByDesc('created_at');

        // Nếu user đăng nhập, lọc đơn theo SĐT hoặc theo tên
        if ($user) {
            $query->where(function ($q) use ($user) {
                if (!empty($user->phone)) {
                    $q->where('customer_phone', $user->phone);
                }

                $q->orWhere('customer_name', $user->name);
            });
        } else {
            // Nếu chưa đăng nhập thì không có đơn nào để hiển thị
            $orders = collect();
            return view('oders.index', [
                'orders'       => $orders,
                'statusFilter' => $statusFilter,
            ]);
        }

        // Filter theo trạng thái (all | pending | confirmed | preparing | shipping | delivered | cancelled)
        if ($statusFilter !== 'all') {
            $query->where('delivery_status', $statusFilter);
        }

        $orders = $query->paginate(10);

        return view('oders.index', compact('orders', 'statusFilter'));
    }

    /**
     * Chi tiết 1 đơn hàng cho user (hóa đơn).
     * Chỉ cho phép xem nếu số điện thoại đơn trùng với phone của user.
     */
    public function userShow(Request $request, $id)
    {
        $user = $request->user();

        $orderQuery = Order::with([
            'details.variant.product',
            'details.variant.values.attribute',
        ])->where('id', $id);

        if ($user) {
            $orderQuery->where(function ($q) use ($user) {
                if (!empty($user->phone)) {
                    $q->where('customer_phone', $user->phone);
                }

                $q->orWhere('customer_name', $user->name);
            });
        }

        $order = $orderQuery->firstOrFail();

        $subtotal = $this->calculateTotal($order);
        $discountAmount = max(0, (int) $subtotal - (int) $order->total);

        return view('oders.show', compact('order', 'subtotal', 'discountAmount'));
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
