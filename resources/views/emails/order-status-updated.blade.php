
<p>Xin chào {{ $order->customer_name }},</p>

<p>
    Đơn hàng <strong>#{{ $order->id }}</strong> đã được cập nhật trạng thái.
</p>
@php
use App\Helpers\OrderStatus;
@endphp
<p>
  Trạng thái cũ:
    <strong>{{ OrderStatus::label($oldStatus) }}</strong><br>

    Trạng thái mới:
    <strong>{{ OrderStatus::label($order->delivery_status) }}</strong>
</p>

<p>
    Tổng thanh toán: <strong>{{ number_format($order->total) }} đ</strong>
</p>

<p>Cảm ơn bạn đã mua hàng!</p>

