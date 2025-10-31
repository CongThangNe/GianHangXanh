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
                    <th>Người đặt</th>
                    <th>Tổng</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $o)
                <tr>
                    <td>{{ $o->id }}</td>
                    <td>{{ $o->user_id }}</td>
                    <td>{{ number_format($o->total_price,0,',','.') }}₫</td>
                    <td>{{ $o->status }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Chưa có đơn hàng nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $orders->links() }}
    </div>

</div>
@endsection
