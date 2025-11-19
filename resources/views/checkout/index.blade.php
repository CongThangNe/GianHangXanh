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

        <form id="payment-form" action="{{ route('checkout.process') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-check">
                    <input type="radio" name="payment_method" value="cod" class="form-check-input" required checked>
                    <span class="form-check-label">Thanh toán khi nhận hàng (COD)</span>
                </label>
            </div>
            <div class="mb-3">
                <label class="form-check">
                    <input type="radio" name="payment_method" value="online" class="form-check-input" id="zalopay-option">
                    <span class="form-check-label">Thanh toán bằng ZaloPay</span>
                </label>
            </div>

            <!-- QR: CHỈ HIỆN KHI ZALOPAY, VỪA PHẢI -->
            <div id="qr-container" class="text-center mt-4 d-none">
                <h5 class="text-success">Quét mã QR để thanh toán</h5>
                <div class="border rounded p-3 bg-light d-inline-block">
                    <img id="qr-image" src="" alt="QR ZaloPay" class="img-fluid" style="width: 280px; height: 280px;">
                </div>
                <p class="mt-3 text-muted">
                    <small>Tổng: <strong id="qr-total" class="text-dark"></strong></small>
                </p>
            </div>

            <button type="submit" class="btn btn-success btn-lg mt-4 w-100">Xác Nhận Thanh Toán</button>
        </form>
    @endif
</div>

<script>
    const zalopayOption = document.getElementById('zalopay-option');
    const qrContainer = document.getElementById('qr-container');
    const qrImage = document.getElementById('qr-image');
    const qrTotal = document.getElementById('qr-total');

    // Khi chọn ZaloPay → hiện QR
    zalopayOption.addEventListener('change', function() {
        if (this.checked) {
            const total = {{ $total }};
            const orderCode = 'DH' + Math.floor(Math.random() * 100000).toString().padStart(5, '0');
            const url = `https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=zalopay://pay?amount=${total}&description=Thanh toan don ${orderCode}`;

            qrImage.src = url;
            qrTotal.textContent = new Intl.NumberFormat('vi-VN').format(total) + '₫';
            qrContainer.classList.remove('d-none');
        }
    });

    // Khi chọn COD → ẩn QR
    document.querySelector('input[value="cod"]').addEventListener('change', function() {
        if (this.checked) {
            qrContainer.classList.add('d-none');
        }
    });

    // Khi submit → nếu là ZaloPay, tạo đơn & hiện QR (nếu chưa)
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        if (method === 'online' && qrContainer.classList.contains('d-none')) {
            e.preventDefault();
            zalopayOption.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection