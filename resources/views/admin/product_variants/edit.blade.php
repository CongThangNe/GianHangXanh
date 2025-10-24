@extends('layouts.app')
@section('title','Sửa Mã Giảm Giá')
@section('content')
    <h1>Edit Variant for Product: {{ $product->name }}</h1>
    <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="sku">SKU</label>
            <input type="text" name="sku" class="form-control" value="{{ $variant->sku }}">
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" class="form-control" value="{{ $variant->price }}" required>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" class="form-control" value="{{ $variant->quantity }}" required>
        </div>
        <div class="form-group">
            <label>Attribute Values</label>
            @foreach ($attributes as $attribute)
                <div>
                    <strong>{{ $attribute->name }}</strong>
                    @foreach ($attribute->values as $value)
                        <div class="form-check">
                            <input type="checkbox" name="attribute_values[]" value="{{ $value->id }}" class="form-check-input" {{ in_array($value->id, $selectedValues) ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $value->value }}</label>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection