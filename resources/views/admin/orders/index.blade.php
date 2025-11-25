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
                    <th>Trạng thái</th>
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
                        @if($o->status == 'paid')
                            <span class="badge bg-success">Đã thanh toán</span>
                        @elseif($o->status == 'pending')
                            <span class="badge bg-warning">Chờ thanh toán</span>
                        @else
                            <span class="badge bg-secondary">{{ $o->status }}</span>
                        @endif
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
                    <td colspan="8" class="text-center text-muted">Chưa có đơn hàng nào</td>
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
