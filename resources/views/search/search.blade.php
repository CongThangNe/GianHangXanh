@extends('layouts.app')
@section('title', (isset($keyword) && $keyword)
    ? ('Kết quả tìm kiếm: ' . $keyword)
    : (isset($category) ? ('Danh mục: ' . $category->name) : 'Tất cả sản phẩm'))

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">

    <div class="flex items-end justify-between gap-4 mb-4">
        @if(isset($keyword) && $keyword)
            <h2 class="text-2xl font-bold">Kết quả tìm kiếm cho: "{{ $keyword }}"</h2>
        @elseif(isset($category))
            <h2 class="text-2xl font-bold">Danh mục: {{ $category->name }}</h2>
        @else
            <h2 class="text-2xl font-bold">Tất cả sản phẩm</h2>
        @endif

        <a href="{{ route('home') }}" class="text-sm font-semibold hover:underline">
            ← Về trang chủ
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5 justify-items-center">
        @forelse($products as $p)
            <div class="w-full max-w-full sm:max-w-[320px] flex flex-col gap-4 rounded-xl bg-surface-light dark:bg-surface-dark
                        shadow-sm border border-border-light dark:border-border-dark overflow-hidden">

                <div class="aspect-square bg-white dark:bg-black/20 flex items-center justify-center p-3">
                    <img
                        src="{{ $p->image_url }}"
                        alt="{{ $p->name }}"
                        class="w-full h-full object-contain"
                        loading="lazy"
                    >
                </div>

                <div class="flex flex-col flex-1 justify-between p-4 pt-2 gap-3">
                    <div>
                        <p class="text-base font-medium line-clamp-2">{{ $p->name }}</p>
                        <p class="text-sm text-subtle-light dark:text-subtle-dark font-bold">
                            {{ number_format($p->price, 0, ',', '.') }}₫
                        </p>
                    </div>

                    <a href="{{ route('product.show', $p->id) }}"
                       class="flex items-center justify-center rounded-lg h-10 px-4
                              bg-primary/20 dark:bg-primary/30 text-sm font-bold hover:bg-primary/30
                              dark:hover:bg-primary/40 transition">
                        Xem chi tiết
                    </a>
                </div>

            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400">Chưa có sản phẩm nào.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $products->links('vendor.pagination.tailwind') }}
    </div>

</div>
@endsection
