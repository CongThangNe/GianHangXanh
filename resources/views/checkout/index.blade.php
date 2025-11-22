@extends('layouts.app')
@section('title', 'Thanh Toán')
@section('content')
<div class="container py-5">
    <h2 class="mb-4">Thanh Toán</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($cartItems->isEmpty())
        <div class="alert alert-warning">Giỏ hàng trống!</div>
    @else
        <h4>Danh sách sản phẩm</h4>
        <ul class="list-group mb-4">
            @foreach($cartItems as $item)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ $item->variant->product->name ?? 'Sản phẩm' }}</span>
                    <span>{{ $item->quantity }} x {{ number_format($item->price) }}₫</span>
                </li>
            @endforeach
            <li class="list-group-item fw-bold d-flex justify-content-between">
                <span>Tổng cộng:</span>
                <span>{{ number_format($total) }}₫</span>
            </li>
        </ul>

        <form id="checkout-form" method="POST" action="{{ route('checkout.process') }}">
    @csrf
    <div class="row">
        <div class="col-md-7">
            <h4>Thông tin nhận hàng</h4>
            <div class="mb-3"><input type="text" name="customer_name" class="form-control" placeholder="Họ và tên" required></div>
            <div class="mb-3"><input type="text" name="customer_phone" class="form-control" placeholder="Số điện thoại" required></div>
            <div class="mb-3"><input type="text" name="customer_address" class="form-control" placeholder="Địa chỉ giao hàng" required></div>
            <div class="mb-3"><textarea name="note" class="form-control" rows="2" placeholder="Ghi chú (không bắt buộc)"></textarea></div>

            <h4 class="mt-4">Phương thức thanh toán</h4>
            <div class="mb-3">
                <label class="form-check">
                    <input type="radio" name="payment_method" value="cod" class="form-check-input" checked>
                    <span class="form-check-label">Thanh toán khi nhận hàng (COD)</span>
                </label>
            </div>
            <div class="mb-3">
                <label class="form-check">
                    <input type="radio" name="payment_method" value="zalopay" class="form-check-input" id="zalopay-option">
                    <span class="form-check-label">Thanh toán qua ZaloPay</span>
                </label>
            </div>
        </div>

        <div class="col-md-5">
            <!-- QR ZaloPay thật -->
            <div id="qr-container" class="text-center mt-4 d-none">
                <h5 class="text-success">Quét mã QR để thanh toán</h5>
                <div class="border rounded p-3 bg-light d-inline-block">
                    <img id="qr-image" src="" alt="QR ZaloPay" class="img-fluid" style="width:280px;height:280px;">
                </div>
                <p class="mt-3"><strong id="qr-total"></strong></p>
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-primary" id="check-payment">Kiểm tra thanh toán</button>
                </div>
                <div id="payment-status" class="mt-3"></div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-success btn-lg mt-4 w-100">XÁC NHẬN ĐƠN HÀNG</button>
</form>
    @endif
</div>

<script>
document.getElementById('checkout-form').addEventListener('submit', async function(e) {
    const method = document.querySelector('input[name="payment_method"]:checked').value;
    if (method === 'cod') return; // để submit bình thường

    e.preventDefault(); // chặn submit nếu là zalopay

    const formData = new FormData(this);
    const response = await fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });

    const data = await response.json();
    if (!data.success) {
        alert('Có lỗi xảy ra!');
        return;
    }

    // Tạo QR thật từ ZaloPay Deep Link (hoặc dùng API thật nếu bạn đã có app_id, key)
    const deepLink = `zalopay://pay?amount=${data.total}&description=Thanh%20toan%20don%20${data.order_code}`;
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=${encodeURIComponent(deepLink)}`;

    document.getElementById('qr-image').src = qrUrl;
    document.getElementById('qr-total').textContent = new Intl.NumberFormat('vi-VN').format(data.total) + '₫';
    document.getElementById('qr-container').classList.remove('d-none');

    // Polling kiểm tra trạng thái (đơn giản)
    const checkBtn = document.getElementById('check-payment');
    checkBtn.onclick = async () => {
        const res = await fetch(`/check-zalopay-status/${data.order_id}`);
        const result = await res.json();
        if (result.paid) {
            document.getElementById('payment-status').innerHTML = `<div class="alert alert-success">Thanh toán thành công! Đang chuyển về trang chủ...</div>`;
            setTimeout(() => location.href = '/', 2000);
        } else {
            document.getElementById('payment-status').innerHTML = `<div class="alert alert-info">Chưa thấy thanh toán. Vui lòng thử lại.</div>`;
        }
    };
});
</script>
@endsection