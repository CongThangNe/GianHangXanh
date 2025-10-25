@extends('layouts.app')
@section('title','Quản lý đơn hàng')
@section('content')
<h3>Danh sách đơn hàng</h3>
<table class="table table-bordered">
<thead><tr><th>ID</th><th>Người đặt</th><th>Tổng</th><th>Trạng thái</th></tr></thead>
<tbody>
@foreach($orders as $o)
<tr>
    <td>{{ $o->id }}</td>
    <td>{{ $o->user_id }}</td>
    <td>{{ number_format($o->total_price,0,',','.') }}₫</td>
    <td>{{ $o->status }}</td>
</tr>
@endforeach
</tbody>
</table>
{{ $orders->links() }}
@endsection
