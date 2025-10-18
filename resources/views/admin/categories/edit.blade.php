@extends('layouts.app')
@section('title','Sửa danh mục')
@section('content')
<h3>Sửa danh mục</h3>

<form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Tên danh mục</label>
        <input name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
    </div>

    <button class="btn btn-primary">Cập nhật</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection
