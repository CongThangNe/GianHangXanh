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

        <!-- Thông tin sản phẩm -->
        <div class="col-md-6">
            <h2 class="fw-bold mb-3">{{ $product->name }}</h2>
            
            <div class="mb-3">
                <span class="fs-4 fw-semibold text-success">
                    {{ number_format($product->price, 0, ',', '.') }}₫
                </span>
            </div>

            <div class="mb-3">
                <span class="fw-semibold">Danh mục:</span> 
                <span class="text-secondary">{{ $product->category->name ?? 'Chưa có' }}</span>
            </div>

            <div class="mb-3">
                <span class="fw-semibold">Tồn kho:</span> 
                @if($product->stock > 0)
                    <span class="badge bg-success">{{ $product->stock }} sản phẩm</span>
                @else
                    <span class="badge bg-danger">Hết hàng</span>
                @endif
            </div>

            <p class="text-muted mt-3" style="line-height: 1.7;">
                {{ $product->description }}
            </p>

            <!-- Giả lập đánh giá -->
            <div class="mb-4">
                <span class="fw-semibold">Đánh giá: </span>
                <span class="text-warning">
                    ★★★★☆
                </span>
                <small class="text-muted">(128 đánh giá)</small>
            </div>

            <!-- Form thêm giỏ hàng -->
            <form action="#" method="POST" class="d-flex align-items-center">
                @csrf
                <div class="input-group me-3" style="width: 140px;">
                    <button type="button" class="btn btn-outline-success" onclick="changeQty(-1)">−</button>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control text-center">
                    <button type="button" class="btn btn-outline-success" onclick="changeQty(1)">+</button>
                </div>
                <button type="submit" class="btn btn-success px-4" 
                        @if($product->stock == 0) disabled @endif>
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

<!-- Script tăng giảm số lượng -->
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
</script>
@endsection
