@extends('layouts.app')
@section('title','Giỏ hàng')
@section('content')

<div class="container py-4">

    <h3 class="mb-4 fw-bold text-success">
        <i class="bi bi-basket2-fill"></i> Giỏ hàng của bạn
    </h3>
    <!-- $item->name
    $item->price
    $item->quantity
    $item->image_url
    $item->category
    khi nào có dữ liệu thì thay item bằng các dữ liệu thực tế từ controller truyền vào
    -->
    {{-- Nếu giỏ hàng rỗng --}}
    @if(empty($cartItems) || count($cartItems) === 0)
    <div class="text-center py-5">
        <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" width="140">
        <h4 class="mt-3 text-secondary">Giỏ hàng đang trống</h4>
        <a href="{{ url('/') }}" class="btn btn-success mt-3">
            <i class="bi bi-shop"></i> Tiếp tục mua sắm
        </a>
    </div>

    @else
    {{-- Danh sách sản phẩm --}}
    <div class="row">
        <div class="col-lg-8">

            @foreach($cartItems as $item)
            <div class="card mb-3 shadow-sm border-0 rounded-3">
                <div class="card-body p-3">
                    <div class="row align-items-center">

                        {{-- Ảnh sản phẩm --}}
                        <div class="col-3">
                            <img src="{{ $item->image_url ?? '' }}"
                                class="img-fluid rounded"
                                alt="{{ $item->name }}">
                        </div>

                        {{-- Thông tin --}}
                        <div class="col-5">
                            <h5 class="fw-bold mb-1 text-success">{{ $item->name }}</h5>
                            <p class="mb-1 text-muted">Giá:
                                <span class="text-dark fw-semibold">
                                    {{ number_format($item->price, 0, ',', '.') }}₫
                                </span>
                            </p>

                            <p class="small text-muted">
                                Danh mục: {{ $item->category ?? '—' }}
                            </p>
                        </div>

                        {{-- Số lượng --}}
                        <div class="col-2 text-center">
                            <div class="d-flex justify-content-center align-items-center">
                                <button class="btn btn-light btn-sm border">
                                    <i class="bi bi-dash"></i>
                                </button>

                                <input type="text"
                                    value="{{ $item->quantity }}"
                                    class="form-control text-center mx-1"
                                    style="width: 50px;">

                                <button class="btn btn-light btn-sm border">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Xóa --}}
                        <div class="col-2 text-end">
                            <a href="#" class="text-danger fs-4">
                                <i class="bi bi-trash3"></i>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
            @endforeach

        </div>

        {{-- Thanh toán --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Tóm tắt đơn hàng</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính</span>
                        <span class="fw-bold">{{ number_format($totalPrice,0,',','.') }}₫</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển</span>
                        <span class="fw-bold text-success">Miễn phí</span>
                    </div>

                    <div class="border-top mt-3 pt-3 d-flex justify-content-between">
                        <span class="fw-bold fs-5">Tổng cộng</span>
                        <span class="fw-bold fs-5 text-success">
                            {{ number_format($totalPrice,0,',','.') }}₫
                        </span>
                    </div>

                    <a href="#" class="btn btn-success w-100 mt-3 py-2">
                        <i class="bi bi-wallet2"></i> Tiến hành thanh toán
                    </a>

                </div>
            </div>
        </div>
    </div>
    @endif

</div>

@endsection