@extends('layouts.app')
@section('title', isset($keyword) && $keyword ? 'Kết quả tìm kiếm: ' . $keyword : 'Sản phẩm nổi bật')

@section('content')

<div class="max-w-7xl mx-auto py-6 px-4">

    @if(isset($keyword) && $keyword)
        <h2 class="text-2xl font-bold mb-4">Kết quả tìm kiếm cho: "{{ $keyword }}"</h2>
    @else
        <h2 class="text-2xl font-bold mb-4">Sản phẩm nổi bật</h2>
    @endif

    <div class="w-full flex justify-center">
        <div class="flex overflow-x-auto gap-6 py-4 px-2
                    [-ms-scrollbar-style:none] [scrollbar-width:none]
                    [&::-webkit-scrollbar]:hidden">

            @forelse($products as $p)
                <div class="flex flex-col gap-4 rounded-xl bg-surface-light dark:bg-surface-dark
                            shadow-sm min-w-[250px] border border-border-light dark:border-border-dark overflow-hidden">

                    <div class="w-full bg-center bg-no-repeat aspect-square bg-cover"
                        style="background-image: url('{{ $p->image ? asset('storage/'.$p->image) : 'https://via.placeholder.com/300x300?text=No+Image' }}');">
                    </div>

                    <div class="flex flex-col flex-1 justify-between p-4 pt-2 gap-2">
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
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $products->links('vendor.pagination.tailwind') }}
    </div>

</div>

@endsection
