@extends('layouts.app')
@section('title','Sửa biến thể sản phẩm')
@section('content')
<h3>Sửa biến thể sản phẩm</h3>

<form method="POST" action="{{ route('admin.product_variants.update', $variant->id) }}">
    @csrf
    @method('PUT')

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

    <div class="mb-3">
        <label class="form-label">SKU</label>
        <input name="sku" class="form-control" required value="{{ $variant->sku }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Giá</label>
        <input name="price" type="number" class="form-control" required value="{{ $variant->price }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Tồn kho</label>
        <input name="stock" type="number" class="form-control" required value="{{ $variant->stock }}">
    </div>

    <hr>
    <h5>Chọn thuộc tính</h5>

    @foreach($attributes as $attr)
        <div class="mb-3">
            <label class="form-label">{{ $attr->name }}</label>
            <select name="attribute_values[]" class="form-select" required>
                @foreach($attr->values as $value)
                    <option value="{{ $value->id }}" 
                        {{ in_array($value->id, $selectedValues) ? 'selected' : '' }}>
                        {{ $value->value }}
                    </option>
                @endforeach
            </select>
        </div>
    @endforeach

    <button class="btn btn-primary">Cập nhật</button>
    <a href="{{ route('admin.product_variants.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection
