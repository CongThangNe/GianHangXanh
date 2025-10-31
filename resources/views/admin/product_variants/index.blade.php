@extends('layouts.admin')
@section('title','Quản lý biến thể')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Biến thể sản phẩm</h3>
        <a href="{{ route('admin.product_variants.create') }}" class="btn btn-success">Thêm biến thể</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
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
                @forelse($variants as $v)
                <tr>
                    <td>{{ $v->id }}</td>
                    <td>{{ $v->product->name ?? 'Chưa gán' }}</td>
                    <td>{{ $v->sku }}</td>
                    <td>{{ number_format($v->price,0,',','.') }}₫</td>
                    <td>{{ $v->stock }}</td>
                    <td>
                        <a href="{{ route('admin.product_variants.edit', $v->id) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <form action="{{ route('admin.product_variants.destroy', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa biến thể?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Chưa có biến thể</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $variants->links() }}
    </div>
</div>
@endsection
