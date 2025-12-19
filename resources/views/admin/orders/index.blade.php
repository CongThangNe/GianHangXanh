@extends('layouts.admin')
@section('title','Quản lý đơn hàng')

@section('content')
<div class="container-fluid p-4" id="content-area">

    <div class="d-flex justify-content-between mb-3">
        <h3>Danh sách đơn hàng</h3>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Mã đơn</th>
                    <th>Người đặt</th>
                    <th>Số điện thoại</th>
                    <th>Tổng tiền</th>
                    <th>Giao hàng</th>
                    <th>Thanh toán</th>
                    <th>Ngày tạo</th>
                    <th width="120">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $o)
                <tr>
                    <td>{{ $o->id }}</td>

                    <td class="fw-bold">{{ $o->order_code }}</td>

                    <td>{{ $o->customer_name }}</td>

                    <td>{{ $o->customer_phone }}</td>

                    <td class="text-danger fw-bold">
                        {{ number_format($o->total,0,',','.') }}₫
                    </td>

                    <td>
                        @php
                            $deliveryDisplay = [
                                'pending'   => ['Chờ xác nhận',        'bg-warning text-dark'],
                                'confirmed' => ['Đã xác nhận',         'bg-primary'],
                                'preparing' => ['Đang chuẩn bị hàng',  'bg-info text-dark'],
                                'shipping'  => ['Đang vận chuyển',     'bg-info text-dark'],
                                'delivered' => ['Đã giao thành công',  'bg-success'],
                                'cancelled' => ['Đã hủy',              'bg-danger'],
                            ][$o->delivery_status] ?? ['Không xác định', 'bg-secondary'];
                        @endphp
                        <span class="badge {{ $deliveryDisplay[1] }}">
                            {{ $deliveryDisplay[0] }}
                        </span>
                    </td>

                    <td>
                        @php
                            $paymentDisplay = [
                                'unpaid' => ['Chưa thanh toán', 'bg-secondary'],
                                'paid'   => ['Đã thanh toán',  'bg-success'],
                            ][$o->payment_status] ?? ['Không xác định', 'bg-secondary'];
                        @endphp
                        <span class="badge {{ $paymentDisplay[1] }}">
                            {{ $paymentDisplay[0] }}
                        </span>
                    </td>

                    <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>

                    <td>
                        <a href="{{ route('admin.orders.show', $o->id) }}" 
                           class="btn btn-sm btn-primary">
                            Xem
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">Chưa có đơn hàng nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <nav class="mt-4 w-100 d-flex justify-content-center">
        {{ $orders->onEachSide(1)->links('pagination::bootstrap-5') }}
    </nav>
</div>
@endsection
