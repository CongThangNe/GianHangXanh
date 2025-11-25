@extends('layouts.app')
@section('title', 'Thanh Toán')

@section('content')
<div class="container py-5">

    <h2 class="fw-bold mb-4" style="color: #2f8f3a;">Thanh Toán</h2>

    {{-- HIỂN THỊ THÔNG BÁO LỖI --}}
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

                {{-- CỘT 1 --}}
                <div class="col-md-7 order-md-1">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-success text-black fw-bold">
                            Thông tin nhận hàng
                        </div>

                        <div class="card-body">

                            <div class="row mb-3">
                                <label class="col-md-4 fw-semibold">Họ và tên *</label>
                                <div class="col-md-8">
                                    <input type="text" name="customer_name" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-md-4 fw-semibold">Số điện thoại *</label>
                                <div class="col-md-8">
                                    <input type="text" name="customer_phone" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-md-4 fw-semibold">Địa chỉ *</label>
                                <div class="col-md-8">
                                    <input type="text" name="customer_address" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-md-4 fw-semibold">Ghi chú</label>
                                <div class="col-md-8">
                                    <textarea name="note" class="form-control"></textarea>
                                </div>
                            </div>

                            <hr>

                            <h5 class="fw-bold" style="color:#2f8f3a;">Phương thức thanh toán</h5>

                            <div class="form-check mb-2">
                                <input type="radio" name="payment_method" value="cod" checked class="form-check-input" id="cod">
                                <label for="cod" class="form-check-label fw-semibold">COD – Thanh toán khi nhận hàng</label>
                            </div>

                            <div class="form-check">
                                <input type="radio" name="payment_method" value="zalopay" class="form-check-input" id="zalopay">
                                <label for="zalopay" class="form-check-label fw-semibold">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/71/ZaloPay_logo.svg/1024px-ZaloPay_logo.svg.png"
                                         style="height:1.2rem;margin-right:5px;">
                                    Thanh toán qua ZaloPay
                                </label>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- CỘT 2 --}}
                <div class="col-md-5 order-md-2">
                    <h4 class="fw-semibold mb-3" style="color:#2f8f3a;">Đơn hàng của bạn</h4>

                    <ul class="list-group mb-4 shadow-sm border border-success">
                        @foreach ($cartItems as $item)
                            <li class="list-group-item d-flex justify-content-between">
                                <div>
                                    <span class="fw-normal">{{ $item->variant->product->name }}</span>
                                    <small class="d-block text-muted">SL: {{ $item->quantity }} x {{ number_format($item->price) }}₫</small>
                                </div>
                            </li>
                        @endforeach

                        <li class="list-group-item fw-bold d-flex justify-content-between bg-light">
                            <span>Tổng tiền:</span>
                            <span>{{ number_format($total) }}₫</span>
                        </li>
                    </ul>

                    {{-- QR ZALOPAY --}}
                    <div id="qr-container" class="card shadow-sm border-0 text-center d-none">
                        <div class="card-header bg-warning text-dark fw-bold">Quét mã QR để thanh toán</div>
                        <div class="card-body">
                            <p class="text-muted small">Tổng tiền: <span id="qr-total" class="fw-bold text-danger"></span></p>
                            <img id="qr-image" class="img-fluid border p-2 rounded" style="width:220px; height:220px;">
                            <button type="button" id="check-payment" class="btn btn-outline-success fw-bold mt-3">Kiểm tra thanh toán</button>
                            <div id="payment-status" class="mt-3"></div>
                        </div>
                    </div>

                </div>

            </div>

            <button type="submit" id="submit-button" class="btn btn-success btn-lg mt-4 w-100 fw-bold py-3">
                HOÀN TẤT ĐẶT HÀNG
            </button>

        </form>

    @endif {{-- <-- cái này rất nhiều bạn thiếu --}}
</div>


{{-- MODAL LỖI --}}
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-black">
                <h5 class="modal-title">Lỗi thanh toán</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="statusModalBody">
                Có lỗi xảy ra. Vui lòng thử lại!
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// JS code của bạn ở đây (giữ nguyên)
</script>
@endpush

@endsection
