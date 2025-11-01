@extends('layouts.admin')
@section('title','Quản lý Thuộc tính')
@section('content')
<h3>Thuộc tính</h3>
<a href="{{ route('admin.attributes.create') }}" class="btn btn-primary mb-2">Thêm thuộc tính</a>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<table class="table">
    <thead><tr><th>ID</th><th>Tên</th><th>Hành động</th></tr></thead>
    <tbody>
    @foreach($attributes as $a)
        <tr>
            <td>{{ $a->id }}</td>
            <td>{{ $a->name }}</td>
            <td>
                <a href="{{ route('admin.attributes.edit', $a->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                <form method="POST" action="{{ route('admin.attributes.destroy', $a->id) }}" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">Xóa</button></form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
