@extends('layouts.admin')


@section('content')
    <div class="container">
        <h1>Banners</h1>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary mb-3"> + Thêm tin</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Tiêu đề</th>
        <th>Ảnh</th>
        <th>Trạng thái</th>
        <th>Hành động</th>
    </tr>

    @foreach($news as $item)
    <tr>
        <td>{{ $item->id }}</td>
        <td>{{ $item->title }}</td>
        <td>
            @if($item->image)
                <img src="{{ asset('storage/'.$item->image) }}" width="80">
            @endif
        </td>
        <td>{{ $item->status ? 'Hiện' : 'Ẩn' }}</td>
        <td>
            <a href="{{ route('admin.news.edit',$item->id) }}" class="btn btn-warning btn-sm">
                Sửa
            </a>

            <form action="{{ route('admin.news.destroy',$item->id) }}"
                  method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm"
                    onclick="return confirm('Xóa tin này?')">
                    Xóa
                </button>
            </form>
        </td>
    </tr>
    @endforeach
</table>



        {{ $news->links() }}
