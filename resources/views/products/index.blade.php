@extends('layouts.app')
@section('title', isset($keyword) && $keyword ? 'Kết quả tìm kiếm: ' . $keyword : 'Trang chủ')

@section('content')

{{-- ========================= TÌM KIẾM ========================= --}}
@if(isset($keyword) && $keyword)
<div class="mb-4">
    <h4 class="mb-3">Kết quả tìm kiếm cho: "{{ $keyword }}"</h4>

    <div class="row g-4">
        @forelse($products as $p)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                @if($p->image)
                <img src="{{ asset('storage/' . $p->image) }}" class="card-img-top" alt="{{ $p->name }}"
                    style="height:200px; object-fit:cover;">
                @endif

                <div class="card-body d-flex flex-column">
                    <a href="{{ route('product.show', $p->id) }}" class="product-link">
                        <h6 class="card-title fw-bold">{{ $p->name }}</h6>
                    </a>
                    <p class="text-success fw-bold mb-2">
                        {{ number_format($p->price,0,',','.') }}₫
                    </p>

                    <div class="mt-auto">
                        <a href="{{ route('product.show', $p->id) }}"
                            class="btn btn-outline-success btn-sm w-100">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <p>Không tìm thấy sản phẩm nào cho từ khóa "{{ $keyword }}".</p>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>

{{-- ========================= TRANG CHỦ ========================= --}}
@else

{{-- ========================= BANNER ========================= --}}
<div id="banner-slideshow" class="mb-4 rounded-3"
    style="height: 250px; position: relative; overflow: hidden;">

    <div class="slide"
        style="background: url('https://picsum.photos/1200/600?green') center/cover no-repeat;"></div>
    <div class="slide"
        style="background: url('https://picsum.photos/1200/600?forest') center/cover no-repeat;"></div>
    <div class="slide"
        style="background: url('https://picsum.photos/1200/600?leaf') center/cover no-repeat;"></div>

    <div
        class="d-flex flex-column justify-content-center align-items-center h-100 text-center px-3 position-absolute top-0 start-0 w-100">
        <h1 class="fw-bold" style="color: green; font-size: 1.8rem;">Chào mừng đến Gian Hàng Xanh 🌱</h1>
        <p style="color: green; font-size: 1rem;">Thực phẩm sạch - An toàn - Vì một tương lai xanh</p>
        <a href="#products" class="btn btn-success btn-sm mt-2">Khám phá ngay</a>
    </div>
</div>


{{-- ========================= SẢN PHẨM NỔI BẬT ========================= --}}
<div class="mb-4">
    <h4 class="mb-3">Sản phẩm nổi bật</h4>

    <div class="w-full flex justify-center">
        <div class="flex overflow-x-auto gap-6 py-4 px-4
                    [-ms-scrollbar-style:none] [scrollbar-width:none]
                    [&::-webkit-scrollbar]:hidden">

            @forelse($featuredProducts as $p)
            <div
                class="flex flex-col gap-4 rounded-xl shadow-sm min-w-64 border overflow-hidden bg-white dark:bg-gray-800">

                <div class="w-full bg-center bg-no-repeat aspect-square bg-cover"
                    style="background-image: url('{{ $p->image ? asset('storage/' . $p->image) : 'https://via.placeholder.com/300x200?text=No+Image' }}');">
                </div>

                <div class="flex flex-col flex-1 justify-between p-4 pt-0 gap-4">
                    <div>
                        <p class="text-base font-medium">{{ $p->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ number_format($p->price, 0, ',', '.') }}₫
                        </p>
                    </div>

                    <a href="{{ route('product.show', $p->id) }}"
                        class="flex items-center justify-center rounded-lg h-10 px-4 bg-green-100 text-green-700 font-bold hover:bg-green-200">
                        Xem chi tiết
                    </a>
                </div>

            </div>
            @empty
            <p>Chưa có sản phẩm nào.</p>
            @endforelse

        </div>
    </div>

    {{-- Nút xem tất cả --}}
    <div class="text-center mt-3">
        <a href="{{ route('products.all') }}" class="btn btn-outline-success btn-sm">
            Xem tất cả sản phẩm →
        </a>
    </div>
</div>

@endif {{-- END IF SEARCH --}}



{{-- ========================= DANH MỤC ========================= --}}
<section class="py-5 px-4">
    <div class="container mx-auto">
        <h2 class="text-center mb-4">Shop by Category</h2>

        <div class="w-full flex justify-center">
            <div class="flex gap-4 py-2 flex-wrap justify-center">
                @foreach ($categories as $category)
                <a href="{{ url('category/' . $category->id) }}"
                    class="flex flex-col items-center gap-3 p-4 rounded bg-gray-100 hover:shadow border w-40">

                    <div
                        class="flex items-center justify-center bg-blue-500/20 text-blue-500 rounded-full w-16 h-16">
                        <span class="material-symbols-outlined text-3xl">category</span>
                    </div>

                    <p class="text-center font-semibold">{{ $category->name }}</p>
                </a>
                @endforeach
            </div>
        </div>

    </div>
</section>



{{-- ========================= ABOUT ========================= --}}
<div class="my-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center p-4">

        <div class="order-2 md:order-1">
            <h2 class="text-green-400 text-[28px] md:text-3xl font-bold leading-tight mb-4">
                Về Gian Hàng Xanh
            </h2>
            <p class="text-green-800 dark:text-gray-300 mb-4 text-base md:text-lg leading-relaxed">
                Chúng tôi mang đến những sản phẩm phù hợp với môi trường, đảm bảo chất lượng
                và an toàn cho sức khỏe người tiêu dùng.
            </p>
            <a class="inline-flex items-center gap-2 text-green-700 font-bold hover:underline"
                href="{{ url('/') }}">
                <span>Tìm hiểu thêm</span>
            </a>
        </div>

        <div class="order-1 md:order-2">
            <div class="w-full bg-center bg-no-repeat aspect-square md:aspect-[4/3] bg-cover rounded-xl shadow-sm"
                style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCTpJeSELuiMrrj86Qaaf81eCfB22sv3_NWiOdqELPTXGdeBNamkTLHqC_BpATDZgZx8cw_aYNlWxxIYMWO78-EC15gQjzN1rbLx0bZf4TPvg3RN30bzizONx3Tjy6DhTeELOTwc-XOhD45F7frgAp__7yVLnO_7iKibk8QvGjLOoeOl84coMIvQteOd_y6Pd0XjdHJiP0_6u3-D9V0ZIAYXGKIx_s_OcEg7BiVZFH0U_TYHOoRSveSnlAkcojEszs4QZ-Nfpl4lmW8");'>
            </div>
        </div>

    </div>
</div>

@endsection
