@extends('layouts.app')
@section('title', 'Trang chủ')

@section('content')

<!-- Banner -->
<div class="p-5 mb-4 bg-light rounded-3"
    style="background:url('https://picsum.photos/1200/300?green') center/cover no-repeat; color:white;">
    <div class="container py-5 text-center">
        <h1 class="fw-bold" style="color: green;">Chào mừng đến Gian Hàng Xanh 🌱</h1>
        <p class="fs-5" style="color: green;">Thực phẩm sạch - An toàn - Vì một tương lai xanh</p>
        <a href="#products" class="btn btn-success btn-lg">Khám phá ngay</a>
    </div>
</div>

<div class="row mb-4">
    <!-- Sidebar -->
    <div class="col-md-3">
        <h5 class="mb-3">Danh mục</h5>
        <ul class="list-group shadow-sm">
            @foreach ($categories as $c)
            <li class="list-group-item">
                <a href="{{ url('category/' . $c->id) }}" class="text-decoration-none text-dark">
                    {{ $c->name }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>

    <!-- Products -->
    <div class="col-md-9">
        <h4 class="mb-3">Sản phẩm nổi bật</h4>
        <div id="products" class="row g-4">
            @forelse($products as $p)
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    @if ($p->image)
                    @if ($p->image)
                    <img src="{{ asset('storage/' . $p->image) }}" class="card-img-top"
                        alt="{{ $p->name }}" style="height:200px; object-fit:cover;">
                    @else
                    <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top"
                        alt="Không có ảnh" style="height:200px; object-fit:cover;">
                    @endif
                    @endif
                    <div class="card-body d-flex flex-column">
                        <a href="{{ route('product.show', $p->id) }}" class="product-link">
                            <h6 class="card-title fw-bold">{{ $p->name }}</h6>
                        </a>
                        <p class="text-success fw-bold mb-2">{{ number_format($p->price, 0, ',', '.') }}₫</p>
                        <div class="mt-auto">
                            <a href="{{ route('product.show', $p->id) }}"
                                class="btn btn-outline-success btn-sm w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <p>Chưa có sản phẩm nào.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>

<!-- About -->
<div class="bg-light p-4 rounded mt-5">
    <h4 class="text-center">Về Gian Hàng Xanh</h4>
    <p class="text-center">
        Chúng tôi mang đến những sản phẩm phù hợp với môi trường, đảm bảo chất lượng và an toàn cho sức khỏe người tiêu
        dùng,
        với sứ mệnh bảo vệ môi trường và hướng đến một cộng đồng sống xanh.
    </p>
</div>

@endsection