@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="container py-4">
    <div class="row g-5 align-items-center">

        <!-- Hình ảnh sản phẩm -->
        <div class="col-md-6 text-center">
            @if($product->image)
            <img src="{{ asset('storage/products/' . $product->image) }}"
                class="img-fluid rounded shadow-sm border border-light"
                style="max-height: 450px; object-fit: contain;">
            @else
            <img src="https://via.placeholder.com/450x450?text=No+Image"
                class="img-fluid rounded shadow-sm border border-light">
            @endif
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-md-6">

            <h2 class="fw-bold mb-3">{{ $product->name }}</h2>

            <!-- Giá -->
            <div class="mb-3">
                <span id="product-price" class="fs-4 fw-semibold text-success">
                    {{ number_format($product->price, 0, ',', '.') }}₫
                </span>
            </div>

            <!-- Danh mục -->
            <div class="mb-3">
                <span class="fw-semibold">Danh mục: </span>
                <span class="text-secondary">{{ $product->category->name ?? 'Chưa có' }}</span>
            </div>

            <!-- Chọn biến thể -->
            @if($product->variants && $product->variants->count() > 0)
            <div class="mb-3">
                <span class="fw-semibold">Chọn biến thể:</span>
                <div id="variant-selector" class="mt-2">
                    @foreach ($product->variants as $variant)
                    <div class="form-check">
                        <input class="form-check-input variant-radio"
                            type="radio"
                            name="variant"
                            id="variant{{ $variant->id }}"
                            value="{{ $variant->id }}"
                            data-price="{{ number_format($variant->price, 0, ',', '.') }}₫"
                            data-stock="{{ $variant->stock }}">
                        <label class="form-check-label" for="variant{{ $variant->id }}">
                            {{ $variant->attributeValues->pluck('value')->join(' / ') }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Tồn kho -->
            <div class="mb-3">
                <span class="fw-semibold">Tồn kho: </span>
                <span id="stock-info" class="badge bg-secondary">
                    {{ $product->variants->count() > 0 ? 'Chưa chọn biến thể' : $product->stock ?? 'Không có' }}
                </span>
            </div>

            <!-- Mô tả -->
            <p class="text-muted mt-3">{{ $product->description }}</p>

            <!-- Đánh giá -->
            <div class="mb-4">
                <span class="fw-semibold">Đánh giá: </span>
                <span class="text-warning">★★★★☆</span>
                <small class="text-muted">(128 đánh giá)</small>
            </div>

            <!-- Form thêm giỏ hàng -->
            <form id="addToCartForm" action="{{ route('cart.index') }}" method="GET" class="d-flex align-items-center">
                @csrf

                <div class="input-group me-3" style="width: 140px;">
                    <button type="button" class="btn btn-outline-success" onclick="changeQty(-1)">−</button>
                    <input type="number" id="quantity" name="quantity"
                        value="1" min="1" max="1"
                        class="form-control text-center">
                    <button type="button" class="btn btn-outline-success" onclick="changeQty(1)">+</button>
                </div>

                <button type="submit" id="add-to-cart-btn" class="btn btn-success px-4" disabled>
                    <i class="bi bi-cart-plus"></i> Thêm vào giỏ hàng
                </button>
            </form>

        </div>
    </div>

    <!-- Mô tả chi tiết -->
    <div class="mt-5">
        <h4 class="fw-bold mb-3">Mô tả chi tiết</h4>
        <div class="p-3 bg-light rounded">
            <p class="mb-0 text-secondary">{{ $product->description ?? 'Chưa có mô tả chi tiết.' }}</p>
        </div>
    </div>
</div>


<!-- SCRIPT -->
<script>
    /* ----------------- Tăng / giảm số lượng ----------------- */
    function changeQty(change) {
        const input = document.getElementById('quantity');
        let value = parseInt(input.value);
        const max = parseInt(input.max);
        const min = parseInt(input.min);

        value += change;
        if (value < min) value = min;
        if (value > max) value = max;
        input.value = value;
    }

    /* ----------------- Chọn biến thể ----------------- */
    document.querySelectorAll('.variant-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const price = this.dataset.price;
            const stock = parseInt(this.dataset.stock);

            const stockInfo = document.getElementById('stock-info');
            const qty = document.getElementById('quantity');
            const btn = document.getElementById('add-to-cart-btn');

            // Cập nhật giá
            document.getElementById('product-price').textContent = price;

            // Cập nhật tồn kho
            if (stock > 0) {
                stockInfo.className = "badge bg-success";
                stockInfo.textContent = stock + " sản phẩm";
                btn.disabled = false;
            } else {
                stockInfo.className = "badge bg-danger";
                stockInfo.textContent = "Hết hàng";
                btn.disabled = true;
            }

            // Cập nhật giới hạn số lượng
            qty.max = stock;
            qty.value = stock > 0 ? 1 : 0;
        });
    });

    /* ----------------- Kiểm tra khi nhấn Thêm vào giỏ hàng ----------------- */
    document.getElementById("addToCartForm").addEventListener("submit", function(e) {
        const hasVariant = document.querySelectorAll('.variant-radio').length > 0;

        if (hasVariant && !document.querySelector('.variant-radio:checked')) {
            e.preventDefault();
            alert("Vui lòng chọn biến thể trước khi thêm vào giỏ hàng!");
            return;
        }
    });
</script>

@endsection