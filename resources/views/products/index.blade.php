@extends('layouts.app')
@section('title', isset($keyword) && $keyword ? 'Kết quả tìm kiếm: ' . $keyword : 'Trang chủ')

@section('content')


<!-- Banner -->
<div id="banner-slideshow" class="mb-4 rounded-3"
     style="height: 250px; position: relative; overflow: hidden;">

    <div class="slide"
         style="background: url('{{ asset('storage/banners/banner1.jpg') }}') center/cover no-repeat;">
    </div>

    <div class="slide"
         style="background: url('{{ asset('storage/banners/banner2.jpg') }}') center/cover no-repeat;">
    </div>

    {{-- <div class="slide"
         style="background: url('{{ asset('storage/banners/banner3.jpg') }}') center/cover no-repeat;">
    </div> --}}

</div>

    <!-- Nội dung cố định -->
    <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center px-3 position-absolute top-0 start-0 w-100">
        <h1 class="fw-bold" style="color: green; font-size: 1.8rem;">Chào mừng đến Gian Hàng Xanh 🌱</h1>
        <p style="color: green; font-size: 1rem;">Thực phẩm sạch - An toàn - Vì một tương lai xanh</p>
        <a href="#products" class="btn btn-success btn-sm mt-2">Khám phá ngay</a>
    </div>
</div>

<div class="mb-4">
    <!-- Products -->
    <div class="col-12">
        <h4 class="mb-3">Sản phẩm nổi bật</h4>

        <!-- Wrapper căn giữa -->
        <div class="w-full flex justify-center">

            <!-- Scroll ngang + spacing đều -->
            <div class="flex overflow-x-auto gap-6 py-4 px-4
                        [-ms-scrollbar-style:none] [scrollbar-width:none]
                        [&::-webkit-scrollbar]:hidden">

                @forelse($products as $p)
                <div class="flex flex-col gap-4 rounded-xl bg-surface-light dark:bg-surface-dark
                            shadow-sm min-w-64 border border-border-light dark:border-border-dark
                            overflow-hidden">

                    <div class="w-full bg-center bg-no-repeat aspect-square bg-cover"
                        style="background-image: url('{{ $p->image ? asset('storage/' . $p->image) : asset('https://via.placeholder.com/300x200?text=No+Image') }}');">
                    </div>

                    <div class="flex flex-col flex-1 justify-between p-4 pt-0 gap-4">
                        <div>
                            <p class="text-base font-medium">{{ $p->name }}</p>
                            <p class="text-sm text-subtle-light dark:text-subtle-dark">
                                {{ number_format($p->price, 0, ',', '.') }}₫
                            </p>
                        </div>

                        <a href="{{ route('product.show', $p->id) }}"
                            class="flex items-center justify-center rounded-lg h-10 px-4
                                  bg-primary/20 dark:bg-primary/30 text-sm font-bold hover:bg-primary/30
                                  dark:hover:bg-primary/40">
                            Xem chi tiết
                        </a>
                    </div>

                </div>
                @empty
                <p>Chưa có sản phẩm nào.</p>
                @endforelse

            </div>
        </div>
    </div>
</div>



<!-- Shop by Category Section -->
<section class="py-5 px-4">
    <div class="container mx-auto">
        <h2 class="text-center mb-4">Shop by Category</h2>

        <div class="w-full flex justify-center">
            <div class="flex gap-4 py-2 flex-wrap justify-center">
                @foreach ($categories as $category)
                <a href="{{ url('category/' . $category->id) }}"
                    class="flex flex-col items-center gap-3 p-4 rounded bg-gray-100 hover:shadow border w-40">

                    <div class="flex items-center justify-center bg-blue-500/20 text-blue-500 rounded-full w-16 h-16">
                        <span class="material-symbols-outlined text-3xl">category</span>
                    </div>

                    <p class="text-center font-semibold">{{ $category->name }}</p>
                </a>
                @endforeach
            </div>
        </div>

    </div>
</section>





<!-- About -->
<div class="my-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center p-4">
        <!-- Nội dung văn bản -->
        <div class="order-2 md:order-1">
            <h2 class="text-gray-800 dark:text-gray-100 text-[28px] md:text-3xl font-bold leading-tight tracking-tight mb-4">
                Về Gian Hàng Xanh
            </h2>
            <p class="text-gray-600 dark:text-gray-300 mb-4 text-base md:text-lg leading-relaxed">
                Chúng tôi mang đến những sản phẩm phù hợp với môi trường, đảm bảo chất lượng và an toàn cho sức khỏe người tiêu dùng,
                với sứ mệnh bảo vệ môi trường và hướng đến một cộng đồng sống xanh.
            </p>
            <a class="inline-flex items-center gap-2 text-deep-forest-green dark:text-primary font-bold hover:underline" href="{{ url('/') }}">
                <span>Tìm hiểu thêm</span>
            </a>
        </div>

        <!-- Hình ảnh -->
        <div class="order-1 md:order-2">
            <div class="w-full bg-center bg-no-repeat aspect-square md:aspect-[4/3] bg-cover rounded-xl shadow-sm"
                style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCTpJeSELuiMrrj86Qaaf81eCfB22sv3_NWiOdqELPTXGdeBNamkTLHqC_BpATDZgZx8cw_aYNlWxxIYMWO78-EC15gQjzN1rbLx0bZf4TPvg3RN30bzizONx3Tjy6DhTeELOTwc-XOhD45F7frgAp__7yVLnO_7iKibk8QvGjLOoeOl84coMIvQteOd_y6Pd0XjdHJiP0_6u3-D9V0ZIAYXGKIx_s_OcEg7BiVZFH0U_TYHOoRSveSnlAkcojEszs4QZ-Nfpl4lmW8");'
                data-alt="Hình ảnh về Gian Hàng Xanh"></div>
        </div>
    </div>
</div>


@endsection