@extends('layouts.admin')
@section('title','Chi tiết đơn hàng')

@section('content')
<div class="container-fluid p-4">

    <h3 class="mb-4">
        Chi tiết đơn #{{ $order->order_code }}
    </h3>

    <div class="card mb-4">
        <div class="card-header bg-success text-white fw-bold">
            Thông tin khách hàng
        </div>
        <div class="card-body">
            <p><strong>Họ tên:</strong> {{ $order->customer_name }}</p>
            <p><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</p>
            <p><strong>Địa chỉ:</strong> {{ $order->customer_address }}</p>
            @if($order->note)
                <p><strong>Ghi chú:</strong> {{ $order->note }}</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-secondary text-white fw-bold">
            Sản phẩm trong đơn
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Biến thể</th>
                        <th>SL</th>
                        <th>Giá</th>
                        <th>Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->details as $d)
                        <tr>
                            <td>
                                {{ $d->variant->product->name ?? 'Sản phẩm đã xoá' }}
                            </td>

                            <td>
                                @foreach($d->variant->values as $v)
                                    <span class="badge bg-info text-dark me-1">
                                        {{ $v->attribute->name }}: {{ $v->value }}
                                    </span>
                                @endforeach
                            </td>

                            <td>{{ $d->quantity }}</td>

                            <td>{{ number_format($d->price) }}₫</td>

                            <td class="fw-bold text-danger">
                                {{ number_format($d->price * $d->quantity) }}₫
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end">Tổng cộng:</th>
                        <th class="text-danger fw-bold">{{ number_format($order->total) }}₫</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
