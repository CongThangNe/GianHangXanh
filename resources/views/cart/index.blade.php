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
                <span class="text-7xl mb-6 animate-pulse-slow" role="img" aria-label="shopping cart">🛒</span>

                <h2 class="text-2xl font-extrabold text-[#0d1b12] dark:text-white mb-3">Giỏ hàng của bạn đang trống</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8 text-center max-w-md">
                    Hãy thêm những sản phẩm nông sản "Xanh" và chất lượng vào giỏ hàng để tiến hành thanh toán nhé!
                </p>

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

                    @foreach ($cart->items as $item)
                        @php
                            $variant = $item->variant;
                            $product = $variant->product ?? null;
                            $lineTotal = $item->price * $item->quantity;

                            // maxAllowed = qty trong giỏ + stock còn lại (reserve stock)
                            $remaining = (int) ($variant?->stock ?? 0);
                            $maxAllowed = (int) $item->quantity + $remaining;
                        @endphp

                        {{-- ✅ CHỈ 1 CART-ITEM, KHÔNG LỒNG 2 DIV --}}
                        <div class="cart-item flex flex-col gap-4 border-b border-gray-200/80 dark:border-gray-700/80 pb-4 md:grid md:grid-cols-6 md:items-center"
                            data-item-id="{{ $item->id }}" data-unit-price="{{ (int) ($item->price ?? 0) }}"
                            data-max-stock="{{ $maxAllowed }}">

                            {{-- Product --}}
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
                                            class="text-base font-medium leading-normal text-[#0d1b12] dark:text-white hover:underline">
                                            {{ $product->name }}
                                        </a>
                                    @else
                                        <p class="text-base font-medium leading-normal text-[#0d1b12] dark:text-white">
                                            Sản phẩm không tồn tại
                                        </p>
                                    @endif

                                    {{-- Chọn biến thể ngay tại giỏ hàng --}}
                                    @if ($product && $product->variants && $product->variants->count() > 0)
                                        <div class="mt-2 flex items-center gap-2">
                                            <span class="text-xs text-gray-500">Phân loại:</span>
                                            <select
                                                class="variant-select text-sm rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-[#4c9a66]"
                                                data-item-id="{{ $item->id }}">
                                                @foreach ($product->variants as $pv)
                                                    <option value="{{ $pv->id }}" @selected($variant && $pv->id == $variant->id)>
                                                        {{ $pv->variant_label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <p class="text-sm font-normal leading-normal text-[#4c9a66]">
                                            {{ $variant ? $variant->variant_label : '' }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Quantity --}}
                            <div class="flex items-center justify-between md:col-span-1 md:justify-center">
                                <span class="md:hidden text-sm text-gray-600 dark:text-gray-400">Số lượng</span>
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                        class="decrease-btn flex h-7 w-7 items-center justify-center rounded-full bg-primary/20 hover:bg-primary/30 dark:bg-primary/30 dark:hover:bg-primary/40 text-[#13612d] transition-colors">-</button>

                                    <input
                                        class="w-16 sm:w-20 py-1 border-none bg-transparent text-center text-base font-medium focus:outline-0 focus:ring-0"
                                        type="number" value="{{ $item->quantity }}" min="1"
                                        max="{{ $maxAllowed }}">

                                    <button type="button"
                                        class="increase-btn flex h-7 w-7 items-center justify-center rounded-full bg-primary/20 hover:bg-primary/30 dark:bg-primary/30 dark:hover:bg-primary/40 text-[#13612d] transition-colors">+</button>
                                </div>
                            </div>

                            {{-- Price --}}
                            <div class="flex items-center justify-between md:col-span-1 md:justify-end">
                                <span class="md:hidden text-sm text-gray-600 dark:text-gray-400">Giá</span>
                                <p class="line-total text-right text-base font-medium text-[#4c9a66]">
                                    {{ number_format($lineTotal, 0, ',', '.') }}đ
                                </p>
                            </div>

                            {{-- Remove --}}
                            <div class="md:col-span-1 flex justify-end">
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xoá sản phẩm này?');">
                                    @csrf
                                    <button type="submit"
                                        class="text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 p-2 rounded-full hover:bg-red-50 transition-colors">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endforeach

                    <a class="mt-8 inline-flex items-center gap-2 text-sm font-medium text-[#4c9a66] hover:text-[#13612d] hover:underline transition-colors"
                        href="{{ url('/') }}">
                        <span class="material-symbols-outlined"></span> Tiếp tục mua sắm
                    </a>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="lg:col-span-1">
                    <div
                        class="sticky top-24 rounded-xl border border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-gray-800 p-6 shadow-xl">
                        <h2 class="text-lg font-bold text-[#0d1b12] dark:text-white mb-4 border-b pb-2">Tóm tắt đơn hàng
                        </h2>

                        <!-- Mã giảm giá -->
                        <div
                            class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                            <p class="text-sm font-semibold text-[#0d1b12] dark:text-white mb-3 flex items-center gap-2">
                                Mã giảm giá
                            </p>

                            @if (session('discount_code') && isset($discountInfo))
                                <div
                                    class="p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <span class="font-bold text-green-700 dark:text-green-300">
                                                {{ session('discount_code') }}
                                            </span>
                                            <span class="text-sm text-green-600 dark:text-green-400 ml-2">
                                                (Giảm {{ $discountInfo['value'] }})
                                            </span>
                                        </div>
                                        <form action="{{ route('cart.removeDiscount') }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs text-red-600 hover:text-red-800 underline">
                                                Bỏ áp dụng
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <form action="{{ route('cart.applyDiscount') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="code"
                                        class="flex-1 px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#4c9a66] dark:bg-gray-800"
                                        placeholder="Nhập mã giảm giá" required>
                                    <button type="submit"
                                        class="px-3 py-2 bg-[#13612d] text-white font-medium rounded-lg hover:bg-[#1f8045] transition text-sm whitespace-nowrap">
                                        Áp dụng
                                    </button>
                                </form>
                                @if (session('error'))
                                    <p class="text-red-600 dark:text-red-400 text-xs mt-2">{{ session('error') }}</p>
                                @endif
                            @endif
                        </div>

                        <!-- Tóm tắt thanh toán -->
                        <div class="space-y-4">
                            <div class="flex justify-between text-base font-medium text-[#0d1b12] dark:text-white">
                                <span>Tạm tính</span>
                                <span id="subtotal">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                            </div>

                            @if ($discountInfo)
                                <div class="flex justify-between text-base font-medium text-[#13612d] dark:text-green-400">
                                    <span>Giảm giá ({{ $discountInfo['code'] }}):</span>
                                    <span
                                        id="discount-amount">-{{ number_format($discountInfo['amount'], 0, ',', '.') }}đ</span>
                                </div>
                            @endif

                            <div
                                class="flex justify-between text-lg font-bold text-[#0d1b12] dark:text-white border-t border-gray-200/80 dark:border-gray-700/80 pt-4">
                                <span>Thành tiền</span>
                                <span id="total-final">{{ number_format($total, 0, ',', '.') }}đ</span>
                            </div>
                        </div>

                        <form action="{{ route('checkout.index') }}" method="GET">
                            <button type="submit"
                                class="mt-6 w-full py-3 bg-[#13612d] text-white font-bold rounded-lg hover:bg-[#1f8045] transition shadow-lg text-lg">
                                Tiến hành thanh toán
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- JS để cập nhật quantity mà không reload -->
        <script>
            let isUpdating = false;

            function formatCurrency(number) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND',
                    minimumFractionDigits: 0
                }).format(number);
            }

            function showToast(message, isError = false) {
                let toast = document.getElementById('custom-toast');
                if (!toast) {
                    toast = document.createElement('div');
                    toast.id = 'custom-toast';
                    toast.style.cssText =
                        'position:fixed;top:1rem;right:1rem;padding:1rem;border-radius:0.5rem;z-index:9999;transition:opacity 0.4s;';
                    document.body.appendChild(toast);
                }
                toast.className = isError ? 'bg-red-600 text-white' : 'bg-green-600 text-white';
                toast.textContent = message;
                toast.style.opacity = '1';
                setTimeout(() => toast.style.opacity = '0', 3000);
            }

            const refreshSummary = (data) => {
                document.getElementById('subtotal').textContent =
                    formatCurrency(data.subtotal);

                const discountEl = document.getElementById('discount-amount');
                if (discountEl) {
                    discountEl.textContent = '-' + formatCurrency(data.discount_amount ?? 0);
                }

                document.getElementById('total-final').textContent =
                    formatCurrency(data.total);
            };



            document.querySelectorAll('.cart-item').forEach(cartItem => {
                const qtyInput = cartItem.querySelector('input[type="number"]');
                const decreaseBtn = cartItem.querySelector('.decrease-btn');
                const increaseBtn = cartItem.querySelector('.increase-btn');
                const lineTotalEl = cartItem.querySelector('.line-total');
                const variantSelect = cartItem.querySelector('.variant-select');

                const getUnitPrice = () => parseInt(cartItem.dataset.unitPrice || '0');

                // maxStock ở đây là maxAllowed
                const getMaxAllowed = () => parseInt(cartItem.dataset.maxStock || qtyInput.max || '999');

                const setMaxAllowed = (maxAllowed) => {
                    maxAllowed = parseInt(maxAllowed || '999');
                    cartItem.dataset.maxStock = maxAllowed;
                    qtyInput.max = maxAllowed;
                }

                const updateQuantity = (newQty) => {
                    if (isUpdating) return;
                    isUpdating = true;

                    const maxAllowed = getMaxAllowed();
                    newQty = parseInt(newQty) || 1;

                    if (newQty < 1) newQty = 1;
                    if (newQty > maxAllowed) {
                        showToast(`Số lượng không được vượt quá tồn kho (${maxAllowed})`, true);
                        newQty = maxAllowed;
                    }

                    fetch("{{ route('cart.update') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                item_id: cartItem.dataset.itemId,
                                quantity: newQty
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (!data.success) {
                                showToast(data.message || 'Lỗi cập nhật số lượng', true);
                                return;
                            }

                            qtyInput.value = data.quantity;
                            lineTotalEl.textContent = formatCurrency(data.line_total);
                            refreshSummary(data);

                            if (data.max_allowed !== undefined) {
                                setMaxAllowed(data.max_allowed);
                            }

                            showToast('Cập nhật giỏ hàng thành công!');
                        })
                        .catch(() => {
                            showToast('Không thể kết nối server, vui lòng thử lại sau', true);
                        })
                        .finally(() => {
                            isUpdating = false;
                        });
                };


                decreaseBtn.addEventListener('click', () =>
                    updateQuantity(parseInt(qtyInput.value) - 1)
                );

                increaseBtn.addEventListener('click', () =>
                    updateQuantity(parseInt(qtyInput.value) + 1)
                );

                qtyInput.addEventListener('change', () =>
                    updateQuantity(qtyInput.value)
                );


                // Đổi biến thể ngay trong giỏ
                if (variantSelect) {
                    variantSelect.addEventListener('change', (e) => {
                        const newVariantId = e.target.value;

                        fetch("{{ route('cart.updateVariant') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    item_id: cartItem.dataset.itemId,
                                    variant_id: newVariantId
                                })
                            })
                            .then(r => r.json())
                            .then(data => {
                                if (!data.success) {
                                    showToast(data.message || 'Không thể đổi biến thể', true);
                                    return;
                                }

                                if (String(data.item_id) !== String(cartItem.dataset.itemId)) {
                                    window.location.reload();
                                    return;
                                }

                                cartItem.dataset.unitPrice = data.unit_price;

                                if (data.max_allowed !== undefined) {
                                    setMaxAllowed(data.max_allowed);
                                } else if (data.max_stock !== undefined) {
                                    setMaxAllowed(data.max_stock);
                                }

                                qtyInput.value = data.quantity;

                                lineTotalEl.textContent = formatCurrency(data.line_total);
                                refreshSummary(data);
                                showToast('Đổi biến thể thành công!');
                            })
                            .catch(() => showToast('Không thể kết nối server, vui lòng thử lại sau', true));
                    });
                }
            });
        </script>
    @endif
@endsection
