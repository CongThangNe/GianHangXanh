@extends('layouts.app')
@section('title', 'Thanh Toán')

@section('content')
<div class="container py-5">

    <h2 class="fw-bold mb-4" style="color: #2f8f3a;">Thanh Toán</h2>

    {{-- HIỂN THỊ THÔNG BÁO LỖI (Sử dụng Toast thay vì alert) --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- NẾU GIỎ HÀNG TRỐNG --}}
    @if ($cartItems->isEmpty())
        <div class="alert alert-warning text-center fw-bold py-4">
            <span class="fs-4 d-block mb-2">🛒</span>
            Giỏ hàng của bạn đang trống! Vui lòng thêm sản phẩm để tiến hành thanh toán.
        </div>

    @else

        {{-- FORM CHECKOUT --}}
        <form id="checkout-form" method="POST" action="{{ route('checkout.process') }}">
            @csrf

            <div class="row g-4">

                {{-- CỘT 1: THÔNG TIN NHẬN HÀNG VÀ THANH TOÁN (Chiều rộng lớn hơn) --}}
                <div class="col-md-7 order-md-1">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-success text-black fw-bold">
                            Thông tin nhận hàng
                        </div>

                        <div class="card-body">
                            {{-- Sử dụng row/col để căn chỉnh label và input --}}
                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" name="customer_name" class="form-control" required placeholder="Nhập họ tên">
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" name="customer_phone" class="form-control" required placeholder="Nhập số điện thoại">
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <label class="col-md-4 col-form-label fw-semibold">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" name="customer_address" class="form-control" required placeholder="Nhập địa chỉ">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-md-4 col-form-label fw-semibold">Ghi chú</label>
                                <div class="col-md-8">
                                    <textarea name="note" class="form-control" rows="2" placeholder="Ghi chú (không bắt buộc)"></textarea>
                                </div>
                            </div>

                            <hr>

                            {{-- PHƯƠNG THỨC THANH TOÁN --}}
                            <h5 class="fw-bold mb-3" style="color: #2f8f3a;">Chọn phương thức thanh toán</h5>

                            <div class="form-check mb-2">
                                <input type="radio" name="payment_method" value="cod" checked class="form-check-input" id="cod-option">
                                <label class="form-check-label fw-semibold" for="cod-option">
                                    <i class="fas fa-truck text-success me-2"></i> Thanh toán khi nhận hàng (COD)
                                </label>
                            </div>

                            <div class="form-check">
                                <input type="radio" name="payment_method" value="zalopay" class="form-check-input" id="zalopay-option">
                                <label class="form-check-label fw-semibold" for="zalopay-option">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/71/ZaloPay_logo.svg/1024px-ZaloPay_logo.svg.png" 
                                         alt="ZaloPay" style="height: 1.2rem; margin-right: 5px;">
                                    Thanh toán qua ZaloPay
                                </label>
                            </div>

                        </div>
                    </div>
                </div>


                {{-- CỘT 2: DANH SÁCH SẢN PHẨM VÀ QR (Chiều rộng nhỏ hơn) --}}
                <div class="col-md-5 order-md-2">
                    
                    {{-- DANH SÁCH SẢN PHẨM --}}
                    <h4 class="fw-semibold mb-3" style="color: #2f8f3a;">Đơn hàng của bạn</h4>

                    <ul class="list-group mb-4 shadow-sm border border-success">
                        @foreach ($cartItems as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-normal">{{ $item->variant->product->name ?? 'Sản phẩm' }}</span>
                                    @if($item->variant->attribute_value)
                                        <small class="d-block text-muted">Biến thể: {{ $item->variant->attribute_value }}</small>
                                    @endif
                                    <small class="d-block text-muted">SL: {{ $item->quantity }} x {{ number_format($item->price) }}₫</small>
                                </div>

                                <!--  -->
                            </li>
                        @endforeach

                        <li class="list-group-item fw-bold d-flex justify-content-between bg-light">
                            <span>Tổng tiền hàng:</span>
                            <span class="text-dark">{{ number_format($total) }}₫</span>
                        </li>
                        <li class="list-group-item fw-bold d-flex justify-content-between bg-light border-top border-secondary">
                            <span>Phí vận chuyển:</span>
                            <span class="text-success">Miễn phí</span>
                        </li>
                        <li class="list-group-item fw-bold d-flex justify-content-between bg-success text-black fs-5">
                            <span>TỔNG THANH TOÁN:</span>
                            <span>{{ number_format($total) }}₫</span>
                        </li>
                    </ul>

                    {{-- QR ZALOPAY (Khung này sẽ ẩn/hiện bằng JS) --}}
                    <div id="qr-container" class="card shadow-sm border-0 text-center d-none border-warning">
                        <div class="card-header bg-warning text-dark fw-bold">Quét mã QR để thanh toán</div>

                        <div class="card-body">
                            <p class="text-muted small">Quét mã bằng ứng dụng ZaloPay. Tổng tiền: <span class="fw-bold text-danger" id="qr-total"></span></p>
                            
                            {{-- QR Code Placeholder/Image --}}
                            <div class="d-flex justify-content-center mb-3">
                                <img id="qr-image" src="{{ asset('path/to/placeholder/qr.png') }}" class="img-fluid border p-2 rounded" style="width: 250px; height: 250px; object-fit: contain;">
                            </div>
                            
                            <button type="button" class="btn btn-outline-success fw-bold" id="check-payment">
                                <i class="fas fa-check-circle me-1"></i> Kiểm tra thanh toán
                            </button>

                            <div id="payment-status" class="mt-3"></div>
                        </div>
                    </div>
                </div>

            </div>

            <button type="submit" id="submit-button" class="btn btn-success btn-lg mt-4 w-100 fw-bold shadow-lg py-3">
                <i class="fas fa-shopping-bag me-2"></i> HOÀN TẤT ĐẶT HÀNG
            </button>
        </form>

    @endif
</div>

{{-- ================= MODAL THÔNG BÁO (THAY THẾ alert) ================= --}}
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-black">
                <h5 class="modal-title" id="statusModalLabel">Lỗi thanh toán</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="statusModalBody">
                Có lỗi xảy ra trong quá trình tạo đơn hàng/QR. Vui lòng thử lại!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- ================= SCRIPT ZALOPAY ================= --}}
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"></script>
<script>
    // Lắng nghe sự kiện thay đổi phương thức thanh toán
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const isZaloPay = this.value === 'zalopay';
            const qrContainer = document.getElementById('qr-container');
            const submitButton = document.getElementById('submit-button');

            if (isZaloPay) {
                // Ẩn nút submit và hiện container QR
                submitButton.textContent = 'TẠO MÃ QR THANH TOÁN';
                qrContainer.classList.add('d-none'); // Ẩn QR cho lần đầu click
            } else {
                // Đổi nút submit về trạng thái đặt hàng COD
                submitButton.textContent = 'HOÀN TẤT ĐẶT HÀNG';
                qrContainer.classList.add('d-none');
            }
        });
    });

    document.getElementById('checkout-form').addEventListener('submit', async function (e) {
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        const submitButton = document.getElementById('submit-button');
        const originalButtonText = submitButton.textContent;

        // Nếu COD -> submit form bình thường (không cần chặn)
        if (method === 'cod') return;

        e.preventDefault(); // Chặn submit form nếu là ZaloPay

        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Đang tạo QR...';
        
        const formData = new FormData(this);

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                // Thay alert bằng Bootstrap Modal
                const modal = new bootstrap.Modal(document.getElementById('statusModal'));
                document.getElementById('statusModalBody').textContent = data.message || 'Có lỗi xảy ra trong quá trình tạo QR ZaloPay. Vui lòng kiểm tra lại thông tin và thử lại!';
                modal.show();
                return;
            }

            // Tạo Deep Link ZaloPay
            const totalFormatted = new Intl.NumberFormat('vi-VN').format(data.total);
            const deepLink = `zalopay://pay?amount=${data.total}&description=Thanh%20toan%20don%20${data.order_code}`;

            // API tạo QR miễn phí
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(deepLink)}`;

            // Hiển thị QR
            document.getElementById('qr-image').src = qrUrl;
            document.getElementById('qr-total').textContent = totalFormatted + '₫';
            document.getElementById('qr-container').classList.remove('d-none');
            
            // Cuộn lên vị trí QR
            document.getElementById('qr-container').scrollIntoView({ behavior: 'smooth', block: 'start' });


            // Kiểm tra thanh toán
            document.getElementById('check-payment').onclick = async () => {
                const checkButton = document.getElementById('check-payment');
                checkButton.disabled = true;
                checkButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Đang kiểm tra...';
                
                try {
                    const res = await fetch(`/check-zalopay-status/${data.order_id}`);
                    const result = await res.json();

                    const statusBox = document.getElementById('payment-status');

                    if (result.paid) {
                        statusBox.innerHTML = `<div class="alert alert-success mt-2 fw-bold"><i class="fas fa-check-circle me-1"></i> Thanh toán thành công! Đang chuyển hướng...</div>`;
                        setTimeout(() => location.href = data.redirect_url || '/', 2000); // Sử dụng redirect URL từ Backend
                    } else {
                        statusBox.innerHTML = `<div class="alert alert-info mt-2"><i class="fas fa-info-circle me-1"></i> Chưa thấy thanh toán. Vui lòng kiểm tra lại.</div>`;
                    }
                } catch (error) {
                    document.getElementById('payment-status').innerHTML = `<div class="alert alert-danger mt-2">Lỗi kết nối khi kiểm tra trạng thái.</div>`;
                } finally {
                    checkButton.disabled = false;
                    checkButton.innerHTML = '<i class="fas fa-check-circle me-1"></i> Kiểm tra thanh toán';
                }
            };

        } catch (error) {
            // Thay alert bằng Bootstrap Modal
            const modal = new bootstrap.Modal(document.getElementById('statusModal'));
            document.getElementById('statusModalBody').textContent = 'Lỗi kết nối hoặc hệ thống. Vui lòng thử lại sau.';
            modal.show();
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'TẠO MÃ QR THANH TOÁN';
        }
    });
</script>
@endpush

@endsection