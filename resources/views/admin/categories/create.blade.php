@extends('layouts.app')
@section('title','Thêm danh mục')
@section('content')
<h3>Thêm danh mục</h3>
<form method="POST" action="{{ route('admin.categories.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Tên</label>
        <input name="name" class="form-control" required>
    </div>
    <button class="btn btn-primary">Lưu</button>
</form>
@endsection
