@extends('layouts.app')
@section('title','Thêm biến thể sản phẩm')
@section('content')
<h3>Thêm biến thể sản phẩm</h3>

<form method="POST" action="{{ route('admin.product_variants.store') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label">Sản phẩm</label>
        <select name="product_id" class="form-select" required>
            @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">SKU</label>
        <input name="sku" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Giá</label>
        <input name="price" type="number" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Tồn kho</label>
        <input name="stock" type="number" class="form-control" required>
    </div>

    <hr>
    <h5>Chọn thuộc tính cho biến thể</h5>

    @foreach($attributes as $attr)
        <div class="mb-3">
            <label class="form-label">{{ $attr->name }}</label>
            <select name="attributes[{{ $attr->id }}]" class="form-select" required>
                <option value="">-- Chọn {{ strtolower($attr->name) }} --</option>
                @foreach($attr->values as $value)
                    <option value="{{ $value->id }}">{{ $value->value }}</option>
                @endforeach
            </select>
        </div>
    @endforeach

    <button class="btn btn-primary">Lưu</button>
    <a href="{{ route('admin.product_variants.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection
