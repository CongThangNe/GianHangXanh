@php
    // Banner tĩnh cho các trang con (category, chi tiết sản phẩm, liên hệ, ...)
    // Có thể tuỳ biến bằng:
    //   @section('banner_title', '...')
    //   @section('banner_subtitle', '...')
    $title = trim($__env->yieldContent('banner_title')) ?: trim($__env->yieldContent('title')) ?: 'Gian Hàng Xanh';
    $subtitle = trim($__env->yieldContent('banner_subtitle')) ?: '';
    // Dùng ảnh có sẵn trong public/uploads/banners
    $bg = asset('uploads/banners/1767451739.jpg');
@endphp

<div class="mb-4 rounded-3 overflow-hidden relative" style="height: 180px;">
    <div class="absolute inset-0 bg-center bg-cover" style="background-image: url('{{ $bg }}');"></div>
    <div class="absolute inset-0 bg-black/35"></div>

    <div class="relative h-full w-full flex flex-col justify-content-center items-center text-center px-4">
        <h1 class="text-white font-bold text-2xl md:text-3xl">{{ $title }}</h1>
        @if(!empty($subtitle))
            <p class="text-white/90 mt-2 text-sm md:text-base max-w-2xl">{{ $subtitle }}</p>
        @endif
    </div>
</div>
