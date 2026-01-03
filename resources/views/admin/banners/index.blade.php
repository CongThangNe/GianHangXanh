@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Banners</h1>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary mb-3">Thêm Banner</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Đường dẫn</th>
                    <th>Thứ tự</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($banners as $banner)
                    <tr>
                        <td>{{ $banner->id }}</td>
                        <td>
                            @if ($banner->image)
                                <img src="{{ Storage::url($banner->image) }}" alt="" style="max-height:60px;">
                            @endif
                        </td>
                        <td>{{ $banner->title }}</td>
                        <td>{{ $banner->link }}</td>
                        <td>{{ $banner->sort_order }}</td>
                        <td>{{ $banner->status ? 'Hoạt động' : 'Không hoạt động' }}</td>
                        <td>
                            <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-warning">Sửa</a>
                            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST"
                                style="display:inline-block" onsubmit="return confirm('Xoá?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $banners->links() }}
    </div>
@endsection
