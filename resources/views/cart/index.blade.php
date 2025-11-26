@extends('layouts.app')
@section('title', 'Giỏ hàng')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- GIAO DIỆN KHI GIỎ HÀNG TRỐNG --}}
    @if (!$cart || $cart->items->isEmpty())
        <div class="container mx-auto px-4 py-8 md:py-16">
            <div
                class="flex flex-col items-center justify-center py-20 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg transition-all duration-300">
                <!-- Icon/Emoji lớn -->
                <span class="text-7xl mb-6 animate-pulse-slow" role="img" aria-label="shopping cart">🛒</span>

                <h2 class="text-2xl font-extrabold text-[#0d1b12] dark:text-white mb-3">Giỏ hàng của bạn đang trống</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8 text-center max-w-md">
                    Hãy thêm những sản phẩm nông sản "Xanh" và chất lượng vào giỏ hàng để tiến hành thanh toán nhé!
                </p>

                <!-- Nút Kêu gọi Hành động -->
                <a href="{{ url('/') }}"
                    class="flex w-fit cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-[#13612d] h-12 px-8 text-base font-bold text-white transition-colors hover:bg-[#1f8045] shadow-xl">
                    <span class="material-symbols-outlined me-2">storefront</span> Tiếp tục mua sắm ngay
                </a>
            </div>
        </div>
    @else
        {{-- GIAO DIỆN KHI CÓ SẢN PHẨM --}}
        <div class="container mx-auto px-4 py-8 md:py-16">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 lg:gap-12">
                <!-- Left Column: Product List -->
                <div class="lg:col-span-2 space-y-4">
                    <div
                        class="hidden border-b border-gray-200/80 dark:border-gray-700/80 pb-3 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 md:grid md:grid-cols-6 md:gap-4">
                        <span class="col-span-3">Sản phẩm</span>
                        <span class="col-span-1 text-center">Số lượng</span>
                        <span class="col-span-1 text-right">Giá</span>
                        <span class="col-span-1"></span>
                    </div>

                    @php $total = 0; @endphp
                    @foreach ($cart->items as $item)
                        @php
                            $variant = $item->variant;
                            $product = $variant->product ?? null;
                            $lineTotal = $item->price * $item->quantity;
                            $total += $lineTotal;
                        @endphp

                        <div class="cart-item flex flex-col gap-4 border-b border-gray-200/80 dark:border-gray-700/80 pb-4 md:grid md:grid-cols-6 md:items-center"
                            data-item-id="{{ $item->id }}">
                            <div class="flex items-start gap-4 md:col-span-3">
                                @if ($product)
                                    @if ($product->image)
                                        <a href="{{ route('product.show', $product->id) }}">
                                            <div class="h-24 w-24 shrink-0 overflow-hidden rounded-lg bg-gray-200">
                                                <img class="h-full w-full object-cover"
                                                    src="{{ asset('storage/' . $product->image) }}"
                                                    alt="{{ $product->name }}">
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ route('product.show', $product->id) }}">
                                            <div
                                                class="h-24 w-24 shrink-0 overflow-hidden rounded-lg bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-500">No Image</span>
                                            </div>
                                        </a>
                                    @endif
                                @else
                                    <div
                                        class="h-24 w-24 shrink-0 overflow-hidden rounded-lg bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">No Image</span>
                                    </div>
                                @endif
                                <div class="flex flex-1 flex-col justify-center pt-2">
                                    @if ($product)
                                        <a href="{{ route('product.show', $product->id) }}"
                                            class="text-base font-medium leading-normal text-[#0d1b12] dark:text-white hover:underline">{{ $product->name }}</a>
                                    @else
                                        <p class="text-base font-medium leading-normal text-[#0d1b12] dark:text-white">Sản
                                            phẩm không tồn tại</p>
                                    @endif
                                    <p class="text-sm font-normal leading-normal text-[#4c9a66]">
                                        {{ $variant ? $variant->attribute_value : '' }}</p>
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div class="flex items-center justify-between md:col-span-1 md:justify-center">
                                <span class="md:hidden text-sm text-gray-600 dark:text-gray-400">Số lượng</span>
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                        class="decrease-btn flex h-7 w-7 items-center justify-center rounded-full bg-primary/20 hover:bg-primary/30 dark:bg-primary/30 dark:hover:bg-primary/40 text-[#13612d] transition-colors">-</button>
                                    <input
                                        class="w-12 border-none bg-transparent text-center text-base font-medium focus:outline-0 focus:ring-0"
                                        type="number" value="{{ $item->quantity }}" min="1"
                                        max="{{ $variant ? $variant->stock : 999 }}">
                                    <button type="button"
                                        class="increase-btn flex h-7 w-7 items-center justify-center rounded-full bg-primary/20 hover:bg-primary/30 dark:bg-primary/30 dark:hover:bg-primary/40 text-[#13612d] transition-colors">+</button>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="flex items-center justify-between md:col-span-1 md:justify-end">
                                <span class="md:hidden text-sm text-gray-600 dark:text-gray-400">Giá</span>
                                <p class="line-total text-right text-base font-medium text-[#4c9a66]">
                                    {{ number_format($lineTotal, 0, ',', '.') }}₫</p>
                            </div>

                            <!-- Remove -->
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn xoá sản phẩm này?');">

                                @csrf
                                <button type="submit"
                                    class="text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 p-2 rounded-full hover:bg-red-50 transition-colors">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </form>

                        </div>
                    @endforeach

                    <a class="mt-8 inline-flex items-center gap-2 text-sm font-medium text-[#4c9a66] hover:text-[#13612d] hover:underline transition-colors"
                        href="{{ url('/') }}">
                        <span class="material-symbols-outlined">arrow_back</span> Tiếp tục mua sắm
                    </a>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="lg:col-span-1">
                    <div
                        class="sticky top-24 rounded-xl border border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-gray-800 p-6 shadow-xl">
                        <h2 class="text-lg font-bold text-[#0d1b12] dark:text-white mb-4 border-b pb-2">Tóm tắt đơn hàng
                        </h2>
                        <div class="mt-6 space-y-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Tổng phụ</span>
                                <span
                                    class="font-medium total-amount text-gray-800 dark:text-white">{{ number_format($total, 0, ',', '.') }}₫</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Phí vận chuyển</span>
                                <span class="font-medium text-[#4c9a66]">Miễn phí</span>
                            </div>
                        </div>
                        <div class="mt-6 border-t border-gray-200/80 dark:border-gray-700/80 pt-4">
                            <div class="flex justify-between font-bold text-lg">
                                <span>Tổng cộng</span>
                                <span class="total-amount text-[#13612d]">{{ number_format($total, 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                        <form action="{{ route('checkout.index') }}" method="GET">
                            <button type="submit"
                                class="mt-8 flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-[#13612d] h-12 text-base font-bold text-white transition-colors hover:bg-[#1f8045] shadow-lg">
                                <span class="material-symbols-outlined me-2">credit_card</span> Tiến hành thanh toán
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- JS để cập nhật quantity mà không reload -->
        <script>
            // Thêm thư viện icon nếu chưa có trong layouts/app
            if (!document.querySelector('link[href*="material-symbols-outlined"]')) {
                const link = document.createElement('link');
                link.href =
                    'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200';
                link.rel = 'stylesheet';
                document.head.appendChild(link);
            }

            // Hàm format tiền tệ
            function formatCurrency(number) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND',
                    minimumFractionDigits: 0
                }).format(number);
            }

            // Hàm hiển thị modal thông báo (thay thế alert)
            function showToast(message, isError = false) {
                // Tùy chỉnh việc hiển thị thông báo, ví dụ dùng một div cố định ở góc màn hình
                let toast = document.getElementById('custom-toast');
                if (!toast) {
                    toast = document.createElement('div');
                    toast.id = 'custom-toast';
                    toast.style.cssText =
                        'position: fixed; top: 1rem; right: 1rem; padding: 1rem; border-radius: 0.5rem; z-index: 1000; transition: opacity 0.3s ease;';
                    document.body.appendChild(toast);
                }

                toast.className = isError ?
                    'bg-red-500 text-white shadow-lg' :
                    'bg-[#13612d] text-white shadow-lg';
                toast.textContent = message;
                toast.style.opacity = '1';

                setTimeout(() => {
                    toast.style.opacity = '0';
                }, 3000);
            }


            document.querySelectorAll('.cart-item').forEach(item => {
                const qtyInput = item.querySelector('input[type="number"]');
                const decreaseBtn = item.querySelector('.decrease-btn');
                const increaseBtn = item.querySelector('.increase-btn');
                const itemId = item.dataset.itemId;
                const lineTotalEl = item.querySelector('.line-total');
                // Đã sửa lại lỗi: Sử dụng parseInt an toàn hơn
                const maxStock = parseInt(qtyInput.getAttribute('max')) || 999;
                const unitPrice = parseFloat(item.querySelector('.line-total').textContent.replace(/[.₫]/g, '').replace(
                    ',', '.')) / parseInt(qtyInput.value);

                function updateQuantity(newQty) {
                    newQty = parseInt(newQty);

                    if (isNaN(newQty) || newQty < 1) {
                        newQty = 1;
                    }

                    if (newQty > maxStock) {
                        showToast(`Không được vượt quá số lượng tồn kho (${maxStock})`, true);
                        newQty = maxStock;
                    }
                    qtyInput.value = newQty;

                    // Cập nhật giá tạm thời trên giao diện (trước khi fetch)
                    lineTotalEl.textContent = formatCurrency(unitPrice * newQty);


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
                            if (data.success) {
                                // Cập nhật giá trị chính xác từ API
                                lineTotalEl.textContent = formatCurrency(data.line_total);
                                document.querySelectorAll('.total-amount').forEach(el => {
                                    el.textContent = formatCurrency(data.total);
                                });
                                showToast('Cập nhật giỏ hàng thành công!');
                            } else {
                                showToast(data.message || 'Lỗi khi cập nhật giỏ hàng.', true);
                                // Hoàn lại giá trị cũ nếu thất bại
                                qtyInput.value = data.current_quantity || 1;

                                // Cần reload lại trang hoặc cập nhật lại tổng tiền từ API nếu có lỗi
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            showToast('Lỗi kết nối khi cập nhật giỏ hàng.', true);
                        });
                }

                decreaseBtn.addEventListener('click', () => updateQuantity(parseInt(qtyInput.value) - 1));
                increaseBtn.addEventListener('click', () => updateQuantity(parseInt(qtyInput.value) + 1));
                qtyInput.addEventListener('change', () => updateQuantity(parseInt(qtyInput.value)));
            });
        </script>
    @endif
@endsection
