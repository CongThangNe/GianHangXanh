@extends('layouts.admin')
@section('title', 'Quản lý sản phẩm')

@section('content')
<div class="container-fluid p-4" id="content-area">

    <div class="d-flex justify-content-between mb-3">
        <h3>Danh sách sản phẩm</h3>
        <a href="{{ route('admin.products.create') }}" class="btn btn-success">Thêm sản phẩm</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Giá</th>
                    <th>Danh mục</th>
                    <th>Chức năng</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ number_format($p->price, 0, ',', '.') }}₫</td>
                    <td>{{ $p->category->name ?? 'Chưa gán danh mục' }}</td>
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
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Chưa có sản phẩm nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $products->links() }}
    </div>

</div>
@endsection
