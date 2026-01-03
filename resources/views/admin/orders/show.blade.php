@extends('layouts.admin')
@section('title', 'Đơn hàng #' . $order->order_code)

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Đơn hàng #{{ $order->order_code }}</h3>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            Quay lại danh sách
        </a>
    </div>

    <div class="row g-4">

        <!-- Cột trái: Chi tiết -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Thông tin khách hàng</span>
                        <span class="badge bg-secondary">
                            Mã đơn: {{ $order->order_code }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tên khách hàng</label>
                            <p><b> {{ $order->customer_name }} </b></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <p><b>{{ $order->customer_phone }} </b></p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Địa chỉ giao hàng</label>
                            <p><b>{{ $order->customer_address }} </b></p>
                        </div>
                        @if($order->note)
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Ghi chú đơn hàng</label>
                                <p><b>{{ $order->note }} </b></p>
                            </div>
                        @endif
                    </div>

                    <div class="row text-muted small">
                        <div class="col-md-4">
                            <span class="d-block">Ngày tạo:</span>
                            <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="d-block">Cập nhật:</span>
                            <strong>{{ $order->updated_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div class="col-md-4">
                            <span class="d-block">Phương thức thanh toán:</span>
                            <strong>
                                {{ $order->payment_method === 'zalopay' ? 'ZaloPay' : 'Thanh toán khi nhận hàng (COD)' }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Sản phẩm trong đơn</span>
                    <span class="badge bg-light text-dark">
                        {{ $order->details->count() }} sản phẩm
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 70px;">#</th>
                                    <th>Sản phẩm</th>
                                    <th>Phân loại</th>
                                    <th class="text-center" style="width: 90px;">Số lượng</th>
                                    <th class="text-end" style="width: 130px;">Đơn giá</th>
                                    <th class="text-end" style="width: 130px;">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->details as $index => $detail)
                                    @php
                                        $variant = $detail->variant;
                                        $product = $variant?->product;
                                        $attrs = $variant?->values->map(function ($v) {
                                            return $v->attribute->name . ': ' . $v->value;
                                        })->implode(' / ');
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-semibold">
                                                {{ $product->name ?? 'Sản phẩm đã xoá' }}
                                            </div>
                                            @if($product && $product->sku)
                                                <div class="text-muted small">
                                                    SKU: {{ $product->sku }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attrs)
                                                <span class="badge bg-secondary-subtle border text-dark">
                                                    {{ $attrs }}
                                                </span>
                                            @else
                                                <span class="text-muted small">Không có</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ $detail->quantity }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($detail->price) }}₫
                                        </td>
                                        <td class="text-end fw-semibold">
                                            {{ number_format($detail->price * $detail->quantity) }}₫
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Không có sản phẩm nào trong đơn hàng này.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($order->details->count())
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="5" class="text-end">Tổng tiền hàng (theo chi tiết):</th>
                                        <th class="text-end">
                                            {{ number_format($subtotal) }}₫
                                        </th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Tóm tắt + Trạng thái -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white text-center fw-bold">
                    Tóm tắt thanh toán
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Tiền hàng:</span>
                        <strong>{{ number_format($subtotal) }}₫</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Phí vận chuyển:</span>
                        <strong class="text-success">Miễn phí</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Giảm giá:</span>
                        <strong>-{{ number_format($discountAmount) }}₫</strong>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between text-danger">
                        <span class="fs-5 fw-bold">Thành tiền:</span>
                        <span class="fs-4 fw-bold">{{ number_format($order->total) }}₫</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header bg-dark text-white text-center fw-bold">
                    Trạng thái đơn hàng
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Giao hàng:</span>
                            @php
                                $deliveryLabel = [
                                    'pending'   => 'Chờ xử lý',
                                    'confirmed' => 'Đã xác nhận',
                                    'preparing' => 'Đang chuẩn bị',
                                    'shipping'  => 'Đang giao hàng',
                                    'delivered' => 'Đã giao thành công',
                                    'cancelled' => 'Đã hủy'
                                ][$order->delivery_status] ?? 'Không xác định';
                            @endphp
                            <span class="badge bg-primary">{{ $deliveryLabel }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span>Thanh toán:</span>
                            @php
                                $paymentLabel = [
                                    'unpaid' => 'Chưa thanh toán',
                                    'paid'   => 'Đã thanh toán',
                                ][$order->payment_status] ?? 'Không xác định';
                            @endphp
                            <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $paymentLabel }}
                            </span>
                        </div>
                    </div>

                    @php
                        // Khi đơn đã giao xong hoặc đã hủy thì KHÓA (không cho đổi cả giao hàng + thanh toán)
                        $statusLocked = in_array($order->delivery_status, ['delivered', 'cancelled'], true);
                    @endphp

                    <form action="{{ route('admin.orders.updateStatus', $order) }}"
                          method="POST"
                          class="d-flex flex-wrap gap-2 align-items-center">
                        @csrf
                        @method('PATCH')

                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small mb-0">Giao hàng</span>
                            <select name="delivery_status"
                                    id="delivery_status"
                                    class="form-select form-select-sm w-auto"
                                    {{ $statusLocked ? 'disabled' : '' }}>
                                @foreach([
                                    'pending'   => 'Chờ xử lý',
                                    'confirmed' => 'Đã xác nhận',
                                    'preparing' => 'Đang chuẩn bị',
                                    'shipping'  => 'Đang giao hàng',
                                    'delivered' => 'Đã giao thành công',
                                    'cancelled' => 'Đã hủy'
                                ] as $value => $label)
                                    <option value="{{ $value }}" @selected($order->delivery_status === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small mb-0">Thanh toán</span>
                            <select name="payment_status"
                                    id="payment_status"
                                    class="form-select form-select-sm w-auto"
                                    {{ $statusLocked ? 'disabled' : '' }}>
                                @foreach([
                                    'unpaid' => 'Chưa thanh toán',
                                    'paid'   => 'Đã thanh toán',
                                ] as $value => $label)
                                    <option value="{{ $value }}" @selected($order->payment_status === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                                class="btn btn-sm btn-primary"
                                {{ $statusLocked ? 'disabled' : '' }}>
                            Cập nhật
                        </button>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .bg-secondary-subtle { background-color: #f1f3f5; }
    /* Giữ layout cân: chỉ làm mờ nhẹ khi disabled, không thêm class gây lệch */
    select.form-select:disabled { opacity: .85; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const delivery = document.getElementById('delivery_status');
    const payment  = document.getElementById('payment_status');
    if (!delivery || !payment) return;

    // Nếu payment bị disable thì sẽ KHÔNG gửi lên server -> cần 1 hidden input để giữ giá trị.
    let hiddenPayment = document.getElementById('payment_status_hidden');
    if (!hiddenPayment) {
        hiddenPayment = document.createElement('input');
        hiddenPayment.type = 'hidden';
        hiddenPayment.name = 'payment_status';
        hiddenPayment.id = 'payment_status_hidden';
        payment.closest('form')?.appendChild(hiddenPayment);
    }

    function syncLock() {
        const isDelivered = (delivery.value === 'delivered');
        const isCancelled = (delivery.value === 'cancelled');
        const locked = (isDelivered || isCancelled);

        // Khi chọn "Đã giao thành công" -> tự set thanh toán = paid.
        if (isDelivered) {
            payment.value = 'paid';
        }

        // Disable dropdown để tránh sửa sai, nhưng vẫn gửi giá trị qua hidden input.
        payment.disabled = locked;
        hiddenPayment.value = payment.value;
    }

    delivery.addEventListener('change', syncLock);
    syncLock();
});
</script>
@endsection
