@extends('layouts.app')
@section('title','Sửa thuộc tính')
@section('content')
<h3>Sửa thuộc tính</h3>
<form method="POST" action="{{ route('admin.attributes.update', $attribute->id) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label">Tên thuộc tính</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $attribute->name) }}" required>
        @error('name')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <button class="btn btn-primary">Lưu</button>
    <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection
