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

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">ID</th>
                    <th width="80">Ảnh</th>
                    <th>Tên</th>
                    <th width="120">Giá</th>
                    <th width="120">Tồn kho</th>
                    <th width="160">Danh mục</th>
                    <th width="160">Chức năng</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                @php
                $totalStock = $p->variants->sum('stock') ?? 0;
                @endphp
                <tr>
                    <td>{{ $p->id }}</td>

                    {{-- Cột ảnh --}}
                    <td class="text-center">
                        @if ($p->image)
                        <img src="{{ asset('storage/' . $p->image) }}" alt="Ảnh sản phẩm"
                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px;">
                        @else
                        <span class="text-muted fst-italic">Không có ảnh</span>
                        @endif
                    </td>

                    <td>{{ $p->name }}</td>
                    <td>{{ number_format($p->price, 0, ',', '.') }}₫</td>
                    <td>
                        <span class="{{ $totalStock > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $totalStock }}
                        </span>
                    </td>
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
                    <td colspan="7" class="text-center text-muted">Chưa có sản phẩm nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <nav class="mt-4 w-100 d-flex justify-content-center" aria-label="Product pagination">
        {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
    </nav>

</div>
@endsection