@extends('layouts.app')
@section('title','Quản lý biến thể sản phẩm')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Biến thể sản phẩm</h3>
    <a href="{{ route('admin.product_variants.create') }}" class="btn btn-success">Thêm biến thể</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Sản phẩm</th>
            <th>SKU</th>
            <th>Giá</th>
            <th>Tồn kho</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($variants as $v)
        <tr>
            <td>{{ $v->id }}</td>
            <td>{{ $v->product->name ?? 'Chưa gán sản phẩm' }}</td>
            <td>{{ $v->sku }}</td>
            <td>{{ number_format($v->price, 0, ',', '.') }}₫</td>
            <td>{{ $v->stock }}</td>
            <td>
                <a href="{{ route('admin.product_variants.edit', $v->id) }}" class="btn btn-sm btn-primary">Sửa</a>
                <form action="{{ route('admin.product_variants.destroy', $v->id) }}" method="POST" style="display:inline-block">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa biến thể này?')">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $variants->links() }}
@endsection
