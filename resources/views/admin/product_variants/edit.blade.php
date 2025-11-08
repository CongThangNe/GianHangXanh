@extends('layouts.admin')
@section('title', 'Sửa biến thể sản phẩm')

@section('content')
<div class="container-fluid p-0">
    <h3 class="mb-4">Sửa biến thể sản phẩm</h3>

    <form method="POST" action="{{ route('admin.product_variants.update', $variant->id) }}">
        @csrf
        @method('PUT')

        {{-- Sản phẩm --}}
        <div class="mb-3">
            <label class="form-label">Sản phẩm</label>
            <select name="product_id" class="form-select" required>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ $variant->product_id == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- SKU --}}
        <div class="mb-3">
            <label class="form-label">SKU</label>
            <input name="sku" class="form-control" required value="{{ old('sku', $variant->sku) }}">
        </div>

        {{-- Giá --}}
        <div class="mb-3">
            <label class="form-label">Giá (VNĐ)</label>
            <input name="price" type="number" class="form-control" required value="{{ old('price', $variant->price) }}">
        </div>

        {{-- Tồn kho --}}
        <div class="mb-3">
            <label class="form-label">Tồn kho</label>
            <input name="stock" type="number" class="form-control" required value="{{ old('stock', $variant->stock) }}">
        </div>

        <hr>
        <h5 class="mb-3">Chọn thuộc tính và giá trị</h5>

        {{-- Thuộc tính và giá trị --}}
        @foreach($attributes as $attr)
        <div class="mb-3 border rounded p-3 bg-light">
            <label class="form-label fw-bold">{{ $attr->name }}</label>
            <div class="d-flex flex-wrap gap-3">
                @foreach($attr->values as $value)
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="attributes[{{ $attr->id }}][]"
                               id="value_{{ $attr->id }}_{{ $value->id }}"
                               value="{{ $value->id }}"
                               {{ in_array($value->id, $selectedValues) ? 'checked' : '' }}>
                        <label class="form-check-label" for="value_{{ $attr->id }}_{{ $value->id }}">
                            {{ $value->value }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="mt-4">
            <button class="btn btn-primary">
                <i class="bi bi-save"></i> Cập nhật
            </button>
            <a href="{{ route('admin.product_variants.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </form>
</div>
@endsection
