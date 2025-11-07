@extends('layouts.app')
@section('title', $product->name)
@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-6">
            <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded">
        </div>

        <div class="col-md-6">
            <h2>{{ $product->name }}</h2>
            <p class="text-muted">Giá từ: {{ number_format($product->price, 0, ',', '.') }} đ</p>

            <div class="mb-3">
                <label for="variantSelect" class="form-label fw-bold">Chọn biến thể:</label>
                <select id="variantSelect" class="form-select w-75">
                    @foreach ($product->variants as $variant)
                        <option
                            value="{{ $variant->id }}"
                            data-color="{{ $variant->color }}"
                            data-size="{{ $variant->size }}"
                            data-stock="{{ $variant->stock }}"
                            data-price="{{ $variant->price }}"
                            data-product="{{ $product->name }}">
                            {{ $variant->color }} / {{ $variant->size }} - Còn {{ $variant->stock }} sản phẩm
                        </option>
                    @endforeach
                </select>
            </div>

            <button id="addToCartBtn" class="btn btn-success">Thêm vào giỏ</button>

            <div id="stockMessage" class="mt-2 text-danger"></div>
        </div>
    </div>
</div>

{{-- Import JS xử lý giỏ hàng --}}
<script src="{{ asset('js/cart.js') }}"></script>

<script>
document.getElementById('addToCartBtn').addEventListener('click', function() {
    const select = document.getElementById('variantSelect');
    const selected = select.options[select.selectedIndex];

    const variant = {
        id: selected.value,
        color: selected.dataset.color,
        size: selected.dataset.size,
        price: parseFloat(selected.dataset.price),
        stock: parseInt(selected.dataset.stock),
        product_name: selected.dataset.product
    };

    if (variant.stock <= 0) {
        document.getElementById('stockMessage').innerText = "Biến thể này đã hết hàng!";
        return;
    } else {
        document.getElementById('stockMessage').innerText = "";
    }

    addToCart(variant);
});
</script>
@endsection
