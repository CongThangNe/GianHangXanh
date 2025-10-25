@extends('layouts.app')
@section('title','Chi tiết đơn')
@section('content')
<h3>Chi tiết đơn #{{ $order->id }}</h3>
<table class="table table-bordered">
<thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Giá</th></tr></thead>
<tbody>
@foreach($order->items as $it)
<tr>
    <td>{{ $it->product->name ?? '' }}</td>
    <td>{{ $it->quantity }}</td>
    <td>{{ number_format($it->price,0,',','.') }}₫</td>
</tr>
@endforeach
</tbody>
</table>
@endsection
