@extends('layouts.app')
@section('title','Sửa sản phẩm')
@section('content')
<h3>Sửa sản phẩm</h3>

<form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Tên</label>
        <input name="name" class="form-control" required value="{{ old('name', $product->name) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Giá</label>
        <input name="price" class="form-control" required type="number" value="{{ old('price', $product->price) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Danh mục</label>
        <select name="category_id" class="form-select" required>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ (int)old('category_id', $product->category_id) === $c->id ? 'selected' : '' }}>
                {{ $c->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Ảnh</label>
        @if(!empty($product->image))
            <div class="mb-2">
                <img src="{{ asset('storage/'.$product->image) }}" alt="product image" style="max-width:200px">
            </div>
        @endif
        <input type="file" name="image" class="form-control">
    </div>

    <button class="btn btn-primary">Lưu</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection
