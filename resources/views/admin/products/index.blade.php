@extends('layouts.app')
@section('title', 'Quản lý sản phẩm')
@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h3>Danh sách sản phẩm</h3>
        <a href="{{ route('admin.products.create') }}" class="btn btn-success">Thêm sản phẩm</a>
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Giá</th>
                <th>Danh mục</th>
                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ number_format($p->price, 0, ',', '.') }}₫</td>
                    <td>{{ $p->category->name ?? '' }}</td>
                    <td>
                        <a href="{{ route('admin.products.edit', $p->id) }}" class="btn btn-sm btn-primary">Sửa</a>

                        <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Xóa sản phẩm này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $products->links() }}
@endsection
