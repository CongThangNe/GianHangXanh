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

<!-- Trạng thái đơn hàng -->
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-gradient bg-primary text-white fw-bold">
        Trạng thái đơn hàng
    </div>
    <div class="card-body">
        <div class="row align-items-center mb-3">
            <div class="col-md-5">
                <strong>Trạng thái hiện tại:</strong>
                <span class="badge px-3 py-2 ms-2
                    @php
                        $statusClass = match($order->status) {
                            'pending'    => 'bg-secondary text-white',
                            'confirmed'  => 'bg-info text-white',
                            'preparing'  => 'bg-primary text-white',
                            'shipping'   => 'bg-warning text-dark',
                            'delivered'  => 'bg-success text-white',
                            'cancelled'  => 'bg-danger text-white',
                            default      => 'bg-dark text-white',
                        };
                    @endphp
                    {{ $statusClass }}
                ">
                    {{ [
                        'pending'    => 'Chờ xác nhận',
                        'confirmed'  => 'Đã xác nhận',
                        'preparing'  => 'Đang chuẩn bị',
                        'shipping'   => 'Đang giao',
                        'delivered'  => 'Đã giao',
                        'cancelled'  => 'Đã hủy'
                    ][$order->status] ?? 'Không xác định' }}
                </span>
            </div>

            <div class="col-md-7">
                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="d-flex gap-2 align-items-center">
                    @csrf @method('PATCH')
                    <select name="status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        @foreach([
                            'pending'    => 'Chờ xác nhận',
                            'confirmed'  => 'Đã xác nhận',
                            'preparing'  => 'Đang chuẩn bị',
                            'shipping'   => 'Đang giao',
                            'delivered'  => 'Đã giao',
                            'cancelled'  => 'Đã hủy'
                        ] as $val => $text)
                            <option value="{{ $val }}" {{ $order->status == $val ? 'selected' : '' }}>
                                {{ $text }}
                            </option>
                        @endforeach
                    </select>
                    
                </form>
            </div>
        </div>

        <hr>

        <div class="row small text-muted">
            <div class="col-sm-6">
                <strong>Thanh toán:</strong>
                <span class="badge {{ $order->payment_method === 'zalopay' ? 'bg-success' : 'bg-warning' }} ms-2">
                    {{ $order->payment_method === 'cod' ? 'COD' : 'ZaloPay' }}
                </span>
            </div>
            <div class="col-sm-6 text-end">
                <div><strong>Ngày đặt:</strong> {{ $order->created_at->format('H:i d/m/Y') }}</div>
                @if($order->updated_at->diffInMinutes($order->created_at) > 1)
                    <div class="text-secondary">Cập nhật: {{ $order->updated_at->format('H:i d/m/Y') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

            <!-- Thông tin khách hàng -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white fw-bold">
                    Thông tin khách hàng
                </div>
                <div class="card-body">
                    <p><strong>Họ tên:</strong> {{ $order->customer_name }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</p>
                    <p><strong>Địa chỉ giao:</strong> {{ $order->customer_address }}</p>
                    @if($order->note)
                        <p><strong>Ghi chú:</strong><br>
                            <span class="text-muted">{{ nl2br(e($order->note)) }}</span>
                        </p>
                    @endif
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">
                    Chi tiết sản phẩm ({{ $order->details->count() }} món)
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="80">Hình</th>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th class="text-center">SL</th>
                                <th class="text-end">Giá</th>
                                <th class="text-end">Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->details as $item)
                                <tr>
                                    <td>
                                        @if($item->variant?->product?->image ?? false)
                                            <img src="{{ asset('storage/' . $item->variant->product->image) }}"
                                                 class="rounded" width="60" height="60" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light border d-flex align-items-center justify-content-center rounded"
                                                 style="width:60px;height:60px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="fw-bold">
                                        {{ $item->variant?->product?->name ?? '<em class="text-danger">Sản phẩm đã bị xóa</em>' }}
                                    </td>
                                    <td>
                                        @if($item->variant?->values->count())
                                            @foreach($item->variant->values as $v)
                                                <span class="badge bg-info text-dark small me-1">
                                                    {{ $v->attribute->name ?? '??' }}: {{ $v->value }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">Mặc định</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->price) }}₫</td>
                                    <td class="text-end text-danger fw-bold">
                                        {{ number_format($item->price * $item->quantity) }}₫
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">Không có sản phẩm</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <th colspan="5" class="text-end fw-bold">TỔNG CỘNG:</th>
                                <th class="text-end text-danger fs-4 fw-bold">{{ number_format($order->total) }}₫</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cột phải: Tóm tắt -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white text-center fw-bold">
                    Tóm tắt thanh toán
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Tiền hàng:</span>
                        <strong>{{ number_format($order->total) }}₫</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Phí vận chuyển:</span>
                        <strong class="text-success">Miễn phí</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Giảm giá:</span>
                        <strong>0₫</strong>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between text-danger">
                        <span class="fs-5 fw-bold">Thành tiền:</span>
                        <span class="fs-4 fw-bold">{{ number_format($order->total) }}₫</span>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-orange { background-color: #fd7e14 !important; }
</style>
@endsection
