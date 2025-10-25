@extends('layouts.app')
@section('title','Quản lý danh mục')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Danh mục</h3>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-success">Thêm danh mục</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
<thead>
<tr>
    <th>ID</th>
    <th>Tên</th>
    <th width="150">Hành động</th>
</tr>
</thead>
<tbody>
@foreach($categories as $c)
<tr>
    <td>{{ $c->id }}</td>
    <td>{{ $c->name }}</td>
    <td>
        <a href="{{ route('admin.categories.edit', $c->id) }}" class="btn btn-sm btn-primary">Sửa</a>
        <form action="{{ route('admin.categories.destroy', $c->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa danh mục này?')">Xóa</button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>

{{ $categories->links() }}
@endsection
