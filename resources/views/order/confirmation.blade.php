
@extends('layouts.app')

@section('title','Xác nhận đơn hàng')

@section('content')
<div class="container py-4">

    <div class="alert alert-success">
        <h4 class="fw-bold">🎉 Đặt hàng thành công!</h4>
        <p>Cảm ơn bạn đã mua hàng tại cửa hàng của chúng tôi.</p>
    </div>

    <div class="card shadow-sm p-4 mb-4">
        <h5 class="fw-bold mb-3">Thông tin đơn hàng</h5>

        <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
        <p><strong>Phương thức thanh toán:</strong> 
            @if($order->payment_method === 'cod')
                Thanh toán khi nhận hàng (COD)
            @else
                Thanh toán Online
            @endif
        </p>
        <p><strong>Tổng tiền:</strong> 
            {{ number_format($order->total_price,0,',','.') }}₫
        </p>
    </div>

    <div class="card shadow-sm p-4 mb-4">
        <h5 class="fw-bold mb-3">Chi tiết sản phẩm</h5>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th width="100">SL</th>
                    <th width="150">Giá</th>
                    <th width="150">Tổng</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->details as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'Sản phẩm không tồn tại' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price,0,',','.') }}₫</td>
                        <td>{{ number_format($item->price * $item->quantity,0,',','.') }}₫</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="text-end">
        <a href="/" class="btn btn-primary">⬅ Tiếp tục mua sắm</a>
    </div>

</div>
@endsection
