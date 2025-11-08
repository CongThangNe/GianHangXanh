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
                     alt="{{ $product->name }}" 
                     style="max-height: 450px; object-fit: contain;">
            @else
                <img src="https://via.placeholder.com/450x450?text=No+Image" 
                     class="img-fluid rounded shadow-sm border border-light" 
                     alt="No image available">
            @endif
        </div>
        <div class="col-md-6">
            <h2 class="fw-bold mb-3">{{ $product->name }}</h2>
            
            <!-- Giá sản phẩm (sẽ thay đổi khi chọn biến thể) -->
            <div class="mb-3">
                <span id="product-price" class="fs-4 fw-semibold text-success">
                    {{ number_format($product->price, 0, ',', '.') }}₫
                </span>
            </div>

            <div class="mb-3">
                <span class="fw-semibold">Danh mục:</span> 
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
                                       data-stock="{{ $variant->stock }}"
                                       data-price="{{ number_format($variant->price, 0, ',', '.') }}₫">
                                <label class="form-check-label" for="variant{{ $variant->id }}">
                                    {{ $variant->attributeValues->pluck('value')->join(' / ') }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Hiển thị tồn kho -->
            <div class="mb-3">
                <span class="fw-semibold">Tồn kho:</span> 
                <span id="stock-info" class="badge bg-secondary">Chưa chọn biến thể</span>
            </div>

            <p class="text-muted mt-3" style="line-height: 1.7;">
                {{ $product->description }}
            </p>

            <!-- Giả lập đánh giá -->
            <div class="mb-4">
                <span class="fw-semibold">Đánh giá: </span>
                <span class="text-warning">★★★★☆</span>
                <small class="text-muted">(128 đánh giá)</small>
            </div>

            <!-- Form thêm giỏ hàng -->
            <form action="#" method="POST" class="d-flex align-items-center">
                @csrf
                <div class="input-group me-3" style="width: 140px;">
                    <button type="button" class="btn btn-outline-success" onclick="changeQty(-1)">−</button>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="1" class="form-control text-center">
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

<!-- Script xử lý biến thể & tăng giảm số lượng -->
<script>
function changeQty(change) {
    const qtyInput = document.getElementById('quantity');
    let value = parseInt(qtyInput.value);
    const max = parseInt(qtyInput.max);
    const min = parseInt(qtyInput.min);

    value += change;
    if (value < min) value = min;
    if (value > max) value = max;

    qtyInput.value = value;
}

document.querySelectorAll('.variant-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        const stock = parseInt(this.dataset.stock);
        const price = this.dataset.price;

        const stockInfo = document.getElementById('stock-info');
        const qtyInput = document.getElementById('quantity');
        const addToCartBtn = document.getElementById('add-to-cart-btn');

        // Hiển thị tồn kho
        if (stock > 0) {
            stockInfo.className = 'badge bg-success';
            stockInfo.textContent = stock + ' sản phẩm';
            addToCartBtn.disabled = false;
        } else {
            stockInfo.className = 'badge bg-danger';
            stockInfo.textContent = 'Hết hàng';
            addToCartBtn.disabled = true;
        }

        // Cập nhật giá
        document.getElementById('product-price').textContent = price;

        // Cập nhật giới hạn số lượng
        qtyInput.max = stock;
        qtyInput.value = stock > 0 ? 1 : 0;
    });
});
</script>
@endsection
