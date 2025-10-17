@extends('layouts.app')
@section('title','Sửa danh mục')
@section('content')
<h3>Sửa danh mục</h3>

<form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Tên</label>
        <input name="name" class="form-control" required value="{{ old('name', $category->name) }}">
    </div>

    <button class="btn btn-primary">Lưu</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection