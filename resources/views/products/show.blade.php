@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16">
    <!-- Left Column: Image Gallery -->
    <div class="flex flex-col gap-4">
        <!-- Main Image: thu nhỏ chiều cao -->
        <div class="w-full h-72 rounded-xl overflow-hidden">
            <img id="main-image" src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/600x450?text=No+Image' }}" alt="Main Product Image" class="w-full h-full object-cover">
        </div>
        <!-- Thumbnails -->
        <div class="grid grid-cols-4 gap-4">
            @for($i = 1; $i <= 4; $i++)
                <img class="w-full aspect-square object-cover rounded-lg @if($i!=1) opacity-75 hover:opacity-100 @endif border-2 border-primary cursor-pointer thumbnail"
                src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/150x150?text=Image+' . $i }}"
                alt="Thumbnail {{ $i }}"
                data-src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/600x450?text=No+Image' }}">
                @endfor
        </div>
    </div>

    <!-- Right Column: Product Information -->
    <div class="flex flex-col gap-6">
        <!-- Title, Price, Rating -->
        <div class="flex flex-col gap-3">
            <h1 class="text-3xl md:text-4xl font-black tracking-[-0.033em]">{{ $product->name }}</h1>
            <p class="text-text-muted-light dark:text-text-muted-dark text-lg">Chúc quý khách 1 ngày thật vui vẻ</p>
            <div class="flex items-center gap-4 pt-2">
                <p class="text-3xl font-bold text-primary">{{ number_format($product->price, 0, ',', '.') }}₫</p>
                <div class="flex items-center gap-2">
                    <div class="flex text-secondary">
                        <span class="material-symbols-outlined text-xl!" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-xl!" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-xl!" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-xl!" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-xl!">star_half</span>
                    </div>
                    <a class="text-sm font-medium text-text-muted-light dark:text-text-muted-dark hover:underline" href="#reviews">(128 reviews)</a>
                </div>
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

        <!-- Quantity & Add to Cart + Hiển thị stock -->
        <div class="flex flex-col sm:flex-row gap-4 items-center mt-4">
            <div class="flex items-center gap-2">
                <div class="flex items-center border border-border-light dark:border-border-dark rounded-md p-1">
                    <button type="button" id="decrease-qty" class="flex items-center justify-center size-9 rounded hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
                        <span class="material-symbols-outlined text-xl">remove</span>
                    </button>
                    <input id="quantity" class="w-12 text-center border-0 bg-transparent focus:ring-0" type="number" value="1" min="1" disabled />
                    <button type="button" id="increase-qty" class="flex items-center justify-center size-9 rounded hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
                        <span class="material-symbols-outlined text-xl">add</span>
                    </button>
                </div>
            </div>
            <button id="add-to-cart-btn" class="w-full flex items-center justify-center gap-2 h-12 px-6 bg-primary text-white rounded-md text-base font-bold hover:opacity-90 transition-opacity" disabled>
                <span class="material-symbols-outlined">add_shopping_cart</span>
                Add to Cart
            </button>
        </div>
    </div>
</div>

<!-- Details Accordion -->
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

<!-- SCRIPT -->
<script>
    // --- Lấy các phần tử ---
    const variantRadios = document.querySelectorAll('.variant-radio');
    const qtyInput = document.getElementById('quantity');
    const addBtn = document.getElementById('add-to-cart-btn');
    const decreaseBtn = document.getElementById('decrease-qty');
    const increaseBtn = document.getElementById('increase-qty');
    const stockInfo = document.getElementById('stock-info');

    let currentStock = 0; // Lưu số lượng hiện tại của biến thể đã chọn

    // --- Hàm cập nhật stock khi chọn biến thể ---
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

    // --- Lắng nghe thay đổi biến thể ---
    variantRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateStock(this);
        });
    });

    // --- Tăng / giảm số lượng ---
    decreaseBtn.addEventListener('click', () => {
        let value = parseInt(qtyInput.value);
        if (value > 1) qtyInput.value = value - 1;
    });

    increaseBtn.addEventListener('click', () => {
        let value = parseInt(qtyInput.value);
        if (value < currentStock) qtyInput.value = value + 1;
        else alert(`Số lượng không được vượt quá ${currentStock} sản phẩm`);
    });

    // --- Kiểm tra khi nhập trực tiếp ---
    qtyInput.addEventListener('input', () => {
        let value = parseInt(qtyInput.value) || 0;
        if (value > currentStock) {
            alert(`Số lượng không được vượt quá ${currentStock} sản phẩm`);
            qtyInput.value = currentStock;
        } else if (value < 1) {
            qtyInput.value = 1;
        }
    });

    // --- Add to Cart ---
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

    // --- Khởi tạo: chưa chọn biến thể ---
    updateStock(null);
</script>


@endsection