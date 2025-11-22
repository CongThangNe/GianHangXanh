@extends('layouts.app')
@section('title','Giỏ hàng')

@section('content')
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    {{ $errors->first() }}
</div>
@endif

@if(!$cart || $cart->items->isEmpty())
<div class="alert alert-info">
    Hiện tại giỏ hàng trống. Hãy thêm sản phẩm vào giỏ.
</div>
@else
<div class="container mx-auto px-4 py-8 md:py-16">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 lg:gap-12">
        <!-- Left Column: Product List -->
        <div class="lg:col-span-2 space-y-4">
            <div class="hidden border-b border-gray-200/80 dark:border-gray-700/80 pb-3 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 md:grid md:grid-cols-6 md:gap-4">
                <span class="col-span-3">Sản phẩm</span>
                <span class="col-span-1 text-center">Số lượng</span>
                <span class="col-span-1 text-right">Giá</span>
                <span class="col-span-1"></span>
            </div>

            @php $total = 0; @endphp
            @foreach($cart->items as $item)
            @php
                $variant = $item->variant;
                $product = $variant->product ?? null;
                $lineTotal = $item->price * $item->quantity;
                $total += $lineTotal;
            @endphp

            <div class="cart-item flex flex-col gap-4 border-b border-gray-200/80 dark:border-gray-700/80 pb-4 md:grid md:grid-cols-6 md:items-center" data-item-id="{{ $item->id }}">
                <div class="flex items-start gap-4 md:col-span-3">
                    @if($product)
                        @if($product->image)
                        <a href="{{ route('product.show', $product->id) }}">
                            <div class="h-24 w-24 shrink-0 overflow-hidden rounded-lg bg-gray-200">
                                <img class="h-full w-full object-cover" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                            </div>
                        </a>
                        @else
                        <a href="{{ route('product.show', $product->id) }}">
                            <div class="h-24 w-24 shrink-0 overflow-hidden rounded-lg bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">No Image</span>
                            </div>
                        </a>
                        @endif
                    @else
                    <div class="h-24 w-24 shrink-0 overflow-hidden rounded-lg bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-500">No Image</span>
                    </div>
                    @endif
                    <div class="flex flex-1 flex-col justify-center pt-2">
                        @if($product)
                            <a href="{{ route('product.show', $product->id) }}" class="text-base font-medium leading-normal text-[#0d1b12] dark:text-white hover:underline">{{ $product->name }}</a>
                        @else
                            <p class="text-base font-medium leading-normal text-[#0d1b12] dark:text-white">Sản phẩm không tồn tại</p>
                        @endif
                        <p class="text-sm font-normal leading-normal text-[#4c9a66]">{{ $variant ? $variant->attributeValues->pluck('value')->join(' / ') : '' }}</p>
                    </div>
                </div>

                <!-- Quantity -->
                <div class="flex items-center justify-between md:col-span-1 md:justify-center">
                    <span class="md:hidden text-sm text-gray-600 dark:text-gray-400">Số lượng</span>
                    <div class="flex items-center gap-2">
                        <button type="button" class="decrease-btn flex h-7 w-7 items-center justify-center rounded-full bg-primary/20 hover:bg-primary/30 dark:bg-primary/30 dark:hover:bg-primary/40">-</button>
                        <input class="w-12 border-none bg-transparent text-center text-base font-medium focus:outline-0 focus:ring-0" type="number" value="{{ $item->quantity }}" min="1" max="{{ $variant ? $variant->stock : 999 }}">
                        <button type="button" class="increase-btn flex h-7 w-7 items-center justify-center rounded-full bg-primary/20 hover:bg-primary/30 dark:bg-primary/30 dark:hover:bg-primary/40">+</button>
                    </div>
                </div>

                <!-- Price -->
                <div class="flex items-center justify-between md:col-span-1 md:justify-end">
                    <span class="md:hidden text-sm text-gray-600 dark:text-gray-400">Giá</span>
                    <p class="line-total text-right text-base font-medium text-[#4c9a66]">{{ number_format($lineTotal,0,',','.') }}₫</p>
                </div>

                <!-- Remove -->
                <div class="flex justify-end md:col-span-1">
                    <form action="{{ route('cart.remove') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xoá sản phẩm này?');">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                        <button type="submit" class="text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach

            <a class="mt-8 inline-flex items-center gap-2 text-sm font-medium text-[#4c9a66] hover:underline" href="{{ url('/') }}">
                Tiếp tục mua sắm
            </a>
        </div>

        <!-- Right Column: Order Summary -->
        <div class="lg:col-span-1">
            <div class="sticky top-24 rounded-xl border border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-background-dark p-6 shadow-sm">
                <h2 class="text-lg font-bold text-[#0d1b12] dark:text-white">Tóm tắt đơn hàng</h2>
                <div class="mt-6 space-y-4">
                    <div class="flex justify-between text-sm">
                        <span>Tổng phụ</span>
                        <span class="font-medium total-amount">{{ number_format($total, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Phí vận chuyển</span>
                        <span class="font-medium">0₫</span>
                    </div>
                </div>
                <div class="mt-6 border-t border-gray-200/80 dark:border-gray-700/80 pt-4">
                    <div class="flex justify-between font-bold">
                        <span>Tổng cộng</span>
                        <span class="total-amount">{{ number_format($total, 0, ',', '.') }}₫</span>
                    </div>
                </div>
                <form action="{{ route('checkout.index') }}" method="GET">
                    <button type="submit" class="mt-8 flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-[#13612d] h-12 text-base font-bold text-white transition-colors hover:bg-opacity-90">
                        Tiến hành thanh toán
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JS để cập nhật quantity mà không reload -->
<script>
document.querySelectorAll('.cart-item').forEach(item => {
    const qtyInput = item.querySelector('input[type="number"]');
    const decreaseBtn = item.querySelector('.decrease-btn');
    const increaseBtn = item.querySelector('.increase-btn');
    const itemId = item.dataset.itemId;
    const lineTotalEl = item.querySelector('.line-total');
    const maxStock = parseInt(qtyInput.max);

    function updateQuantity(newQty) {
        if(newQty < 1) newQty = 1;
        if(newQty > maxStock){
            alert(`Không được vượt quá số lượng tồn kho (${maxStock})`);
            newQty = maxStock;
        }
        qtyInput.value = newQty;

        fetch("{{ route('cart.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                item_id: itemId,
                quantity: newQty
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                lineTotalEl.textContent = new Intl.NumberFormat('vi-VN').format(data.line_total) + '₫';
                document.querySelectorAll('.total-amount').forEach(el => {
                    el.textContent = new Intl.NumberFormat('vi-VN').format(data.total) + '₫';
                });
            }
        });
    }

    decreaseBtn.addEventListener('click', () => updateQuantity(parseInt(qtyInput.value) - 1));
    increaseBtn.addEventListener('click', () => updateQuantity(parseInt(qtyInput.value) + 1));
    qtyInput.addEventListener('change', () => updateQuantity(parseInt(qtyInput.value)));
});
</script>
@endif
@endsection
