@extends('layouts.app')
@section('title','Mã Giảm Giá')
@section('content')
    <h1>Variants for Product: {{ $product->name }}</h1>
    <a href="{{ route('admin.product.variants.create', $product) }}" class="btn btn-primary">Thêm mã giảm giá</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>SKU</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Attributes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($variants as $variant)
                <tr>
                    <td>{{ $variant->id }}</td>
                    <td>{{ $variant->sku }}</td>
                    <td>{{ $variant->price }}</td>
                    <td>{{ $variant->quantity }}</td>
                    <td>
                        @foreach ($variant->attributeValues as $value)
                            {{ $value->attribute->name }}: {{ $value->value }}<br>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('admin.product.variants.edit', [$product, $variant]) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('admin.product.variants.destroy', [$product, $variant]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection