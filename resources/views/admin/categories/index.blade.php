@extends('layouts.app')
@section('title','Quản lý danh mục')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Danh mục</h3>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-success">Thêm danh mục</a>
</div>
<table class="table table-bordered">
<thead><tr><th>ID</th><th>Tên</th></tr></thead>
<tbody>
@foreach($categories as $c)
<tr><td>{{ $c->id }}</td><td>{{ $c->name }}</td></tr>
@endforeach
</tbody>
</table>
{{ $categories->links() }}
@endsection
