@extends('layouts.app')
@section('title', isset($keyword) && $keyword ? 'Kết quả tìm kiếm: ' . $keyword : 'Trang chủ')

@section('content')
{{-- @include('layouts.banner') --}}
{{-- @foreach($products as $product)
<a href="{{ route('product.show', $product->id) }}">
    {{ $product->name }}
</a>
@endforeach --}}



<!-- Banner -->
<div id="banner-slideshow" class="mb-4 rounded-3"
    style="height: 250px; position: relative; overflow: hidden;">

    <div class="slide"
        style="height:100%; background: url('{{ asset('storage/banners/banner1.jpg') }}') center/cover no-repeat; background-size: cover;">
    </div>

    <!-- Nội dung cố định (đặt đúng trong banner để position-absolute hoạt động chuẩn) -->
    <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center px-3 position-absolute top-0 start-0 w-100">
        <h1 class="fw-bold" style="color: green; font-size: 1.8rem;">Chào mừng đến Gian Hàng Xanh 🌱</h1>
        <p style="color: green; font-size: 1rem;">Thực phẩm sạch - An toàn - Vì một tương lai xanh</p>
        <a href="#products" class="btn btn-success btn-sm mt-2">Khám phá ngay</a>
    </div>
</div>

<div class="mb-4" id="products">
    <!-- Products -->
    <div class="col-12">
        <h4 class="mb-3">Sản phẩm nổi bật</h4>

        <div class="w-full flex justify-center">
            <div class="relative w-full">

                <!-- Nút trái -->
                <button type="button"
                    id="top10Prev"
                    aria-label="Cuộn trái"
                    class="absolute left-2 top-1/2 -translate-y-1/2 z-10
                               h-10 w-10 rounded-full
                               bg-black/40 text-white hover:bg-black/60
                               transition flex items-center justify-center
                               opacity-0 pointer-events-none">
                    ‹
                </button>

                <!-- Nút phải -->
                <button type="button"
                    id="top10Next"
                    aria-label="Cuộn phải"
                    class="absolute right-2 top-1/2 -translate-y-1/2 z-10
                               h-10 w-10 rounded-full
                               bg-black/40 text-white hover:bg-black/60
                               transition flex items-center justify-center
                               opacity-0 pointer-events-none">
                    ›
                </button>


                <div class="w-full bg-center bg-no-repeat aspect-square bg-cover"
                    style="background-image: url('{{ $p->image_url ?? 'https://via.placeholder.com/300x200?text=No+Image' }}');">
                </div>
                <!-- Slider -->
                <div id="top10Slider"
                    class="flex overflow-x-auto gap-6 py-4 px-4 scroll-smooth
                            [-ms-scrollbar-style:none] [scrollbar-width:none]
                            [&::-webkit-scrollbar]:hidden">

                    @forelse($products as $p)
                    <div class="flex flex-col gap-4 rounded-xl bg-surface-light dark:bg-surface-dark
                                    shadow-sm min-w-64 border border-border-light dark:border-border-dark
                                    overflow-hidden">

                        <div class="w-full bg-center bg-no-repeat aspect-square bg-cover"
                            style="background-image: url('{{ $p->image_url ?? 'https://via.placeholder.com/300x200?text=No+Image' }}');">
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

{{-- Sản phẩm liên quan theo danh mục sẽ được hiển thị trong trang chi tiết sản phẩm --}}

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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const slider = document.getElementById('top10Slider');
        const prevBtn = document.getElementById('top10Prev');
        const nextBtn = document.getElementById('top10Next');

        if (!slider || !prevBtn || !nextBtn) return;

        const showBtn = (btn) => btn.classList.remove('opacity-0', 'pointer-events-none');
        const hideBtn = (btn) => btn.classList.add('opacity-0', 'pointer-events-none');

        const scrollAmount = () => Math.max(280, Math.floor(slider.clientWidth * 0.8));

        const updateButtons = () => {
            // Không cần nút nếu không có overflow
            const hasOverflow = slider.scrollWidth > slider.clientWidth + 2;
            if (!hasOverflow) {
                hideBtn(prevBtn);
                hideBtn(nextBtn);
                return;
            }

            const atStart = slider.scrollLeft <= 2;
            const atEnd = slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 2;

            if (atStart) hideBtn(prevBtn);
            else showBtn(prevBtn);
            if (atEnd) hideBtn(nextBtn);
            else showBtn(nextBtn);
        };

        prevBtn.addEventListener('click', () => {
            slider.scrollBy({
                left: -scrollAmount(),
                behavior: 'smooth'
            });
        });

        nextBtn.addEventListener('click', () => {
            slider.scrollBy({
                left: scrollAmount(),
                behavior: 'smooth'
            });
        });

        slider.addEventListener('scroll', () => requestAnimationFrame(updateButtons));
        window.addEventListener('resize', () => requestAnimationFrame(updateButtons));

        // Gọi lần đầu + gọi lại sau khi layout/ảnh ổn định
        updateButtons();
        setTimeout(updateButtons, 200);
    });
</script>

@endsection