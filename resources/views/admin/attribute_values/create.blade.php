@extends('layouts.app')
@section('title','Thêm giá trị thuộc tính')
@section('content')
<h3>Thêm giá trị thuộc tính</h3>
<form method="POST" action="{{ route('admin.attribute_values.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Thuộc tính</label>
        <select name="attribute_id" class="form-select" required>
            <option value="">-- Chọn thuộc tính --</option>
            @foreach($attributes as $a)
                <option value="{{ $a->id }}">{{ $a->name }}</option>
            @endforeach
        </select>
        @error('attribute_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Giá trị</label>
        <input type="text" name="value" class="form-control" value="{{ old('value') }}" required>
        @error('value')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <button class="btn btn-primary">Lưu</button>
    <a href="{{ route('admin.attribute_values.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection
