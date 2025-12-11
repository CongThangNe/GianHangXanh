@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16">

        <!-- Left Column: Image Gallery -->
        <div class="flex flex-col gap-4">
            <!-- Main Image -->
            <div class="w-full h-[400px] lg:h-[500px] rounded-xl overflow-hidden border border-border-light dark:border-border-dark">
                <img id="main-image"
                    src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/600x450?text=No+Image' }}"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
            </div>
        </div>

        <!-- Right Column: Product Information -->
        <div class="flex flex-col gap-6">
            <!-- Title, Price -->
            <div class="flex flex-col gap-3">
                <h1 class="text-3xl md:text-4xl font-black tracking-[-0.033em]">{{ $product->name }}</h1>
                <p class="text-text-muted-light dark:text-text-muted-dark text-lg">Chúc quý khách 1 ngày thật vui vẻ</p>
                <div class="flex items-center gap-4 pt-2">
                    <p class="text-3xl font-bold text-primary">{{ number_format($product->price, 0, ',', '.') }}₫</p>
                </div>
            </div>

            <!-- Variant Selector -->
            @if($product->variants && $product->variants->count() > 0)
            <div class="flex flex-col gap-2">
                <label class="text-sm font-bold">Biến thể</label>
                <div id="variant-options" class="flex gap-4 flex-wrap">
                    @foreach ($product->variants as $variant)
                    <label class="flex items-center gap-2 cursor-pointer border border-border-light dark:border-border-dark rounded-md px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <input type="radio" name="variant" class="variant-radio" value="{{ $variant->id }}"
                            data-stock="{{ $variant->stock }}" data-price="{{ $variant->price }}" />
                        <span>{{ $variant->attributeValues->pluck('value')->join(' / ') }}</span>
                    </label>
                    @endforeach
                </div>
                <!-- Stock info -->
                <div id="stock-info" class="mt-2 px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                    Chưa chọn biến thể
                </div>
            </div>
            @endif

            <!-- Quantity & Add to Cart -->
            <!-- Quantity & Add to Cart -->
            <div class="flex flex-col sm:flex-row gap-4 items-center mt-4">
                <!-- Quantity Selector -->
                <div class="flex items-center border border-border-light dark:border-border-dark rounded-md overflow-hidden">
                    <button type="button" id="decrease-qty" class="flex items-center justify-center w-10 h-10 hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
                        <span class="material-symbols-outlined text-xl">remove</span>
                    </button>

                    <input
                        id="quantity"
                        type="number"
                        value="1"
                        min="1"
                        step="1"
                        disabled
                        class="w-16 sm:w-20 text-center border-0 bg-transparent focus:ring-0 text-base" />

                    <button type="button" id="increase-qty" class="flex items-center justify-center w-10 h-10 hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
                        <span class="material-symbols-outlined text-xl">add</span>
                    </button>
                </div>

                <!-- Add to Cart Button -->
                <button id="add-to-cart-btn" class="w-full sm:w-auto flex items-center justify-center gap-2 h-12 px-6 bg-primary text-white rounded-md text-base font-bold hover:opacity-90 transition-opacity" disabled>
                    <span class="material-symbols-outlined">add_shopping_cart</span>
                    Add to Cart
                </button>
            </div>

        </div>
    </div>

    <!-- Description -->
    <div class="w-full mt-16 border-t border-border-light dark:border-border-dark pt-12">
        <div class="flex flex-col gap-4 max-w-3xl mx-auto">
            <details class="group" open="">
                <summary class="flex justify-between items-center cursor-pointer list-none py-4 border-b border-border-light dark:border-border-dark">
                    <span class="text-xl font-bold">Description</span>
                    <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180">expand_more</span>
                </summary>
                <div class="text-text-muted-light dark:text-text-muted-dark pt-4 leading-relaxed">
                    <p>{{ $product->description ?? 'Chưa có mô tả chi tiết.' }}</p>
                </div>
            </details>
        </div>
    </div>

    <!-- Related Products -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="w-full mt-16 border-t border-border-light dark:border-border-dark pt-12">
        <div class="flex flex-col gap-4 max-w-6xl mx-auto">
            <h2 class="text-2xl font-bold">Sản phẩm liên quan</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 mt-4">
                @foreach($relatedProducts as $related)
                <a href="{{ route('products.show', $related->id) }}" class="block border border-border-light dark:border-border-dark rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="w-full h-48">
                        <img src="{{ $related->image ? asset('storage/' . $related->image) : 'https://via.placeholder.com/300x300?text=No+Image' }}"
                            alt="{{ $related->name }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm font-semibold">{{ $related->name }}</h3>
                        <p class="text-primary font-bold">{{ number_format($related->price, 0, ',', '.') }}₫</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<!-- SCRIPT -->
<script>
    const variantRadios = document.querySelectorAll('.variant-radio');
    const qtyInput = document.getElementById('quantity');
    const addBtn = document.getElementById('add-to-cart-btn');
    const decreaseBtn = document.getElementById('decrease-qty');
    const increaseBtn = document.getElementById('increase-qty');
    const stockInfo = document.getElementById('stock-info');
    let currentStock = 0;

    // Cập nhật thông tin tồn kho
    function updateStock(radio) {
        if (!radio) {
            addBtn.disabled = true;
            qtyInput.value = 0;
            qtyInput.disabled = true;
            stockInfo.textContent = "Chưa chọn biến thể";
            stockInfo.className = "mt-2 px-3 py-1 rounded-full text-sm font-medium bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200";
            currentStock = 0;
            return;
        }

        currentStock = parseInt(radio.dataset.stock) || 0;

        if (currentStock <= 0) {
            addBtn.disabled = true;
            qtyInput.value = 0;
            qtyInput.disabled = true;
            stockInfo.textContent = "Hết hàng";
            stockInfo.className = "mt-2 px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-200";
        } else {
            addBtn.disabled = false;
            qtyInput.value = 1;
            qtyInput.max = currentStock;
            qtyInput.disabled = false;
            stockInfo.textContent = `Còn ${currentStock} sản phẩm`;
            stockInfo.className = "mt-2 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-200";
        }
    }

    variantRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateStock(this);
        });
    });

    // Giảm số lượng
    decreaseBtn.addEventListener('click', () => {
        const selectedRadio = document.querySelector('.variant-radio:checked');
        if (!selectedRadio) {
            alert("Vui lòng chọn biến thể trước!");
            return;
        }

        let value = parseInt(qtyInput.value);
        if (value > 1) {
            qtyInput.value = value - 1;
        } else {
            alert("Số lượng không được nhỏ hơn 1!");
        }
    });

    // Tăng số lượng
    increaseBtn.addEventListener('click', () => {
        const selectedRadio = document.querySelector('.variant-radio:checked');
        if (!selectedRadio) {
            alert("Vui lòng chọn biến thể trước!");
            return;
        }
        let value = parseInt(qtyInput.value);
        if (value < currentStock) {
            qtyInput.value = value + 1;
        } else {
            alert(`Số lượng không được vượt quá ${currentStock} sản phẩm`);
        }
    });

    // Nhập tay (chỉ kiểm tra khi rời khỏi ô input)
    qtyInput.addEventListener('change', () => {
        const selectedRadio = document.querySelector('.variant-radio:checked');
        if (!selectedRadio) {
            qtyInput.value = 1;
            return;
        }

        let value = parseInt(qtyInput.value) || 1;

        if (value > currentStock) {
            alert(`Số lượng không được vượt quá ${currentStock} sản phẩm`);
            value = currentStock;
        } else if (value < 1) {
            alert("Số lượng không được nhỏ hơn 1!");
            value = 1;
        }

        qtyInput.value = value;
    });

    // Thêm vào giỏ
    addBtn.addEventListener('click', () => {
        const selectedRadio = document.querySelector('.variant-radio:checked');
        if (!selectedRadio) {
            alert("Vui lòng chọn biến thể trước khi thêm vào giỏ hàng!");
            return;
        }
        const quantity = parseInt(qtyInput.value);
        if (quantity > currentStock) {
            alert(`Số lượng không được vượt quá ${currentStock} sản phẩm`);
            qtyInput.value = currentStock;
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("cart.add") }}';
        form.innerHTML = `
        @csrf
        <input type="hidden" name="variant_id" value="${selectedRadio.value}">
        <input type="hidden" name="quantity" value="${qtyInput.value}">
    `;
        document.body.appendChild(form);
        form.submit();
    });

    // Khởi tạo
    updateStock(null);
</script>

@endsection