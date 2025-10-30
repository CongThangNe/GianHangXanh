@extends('layouts.app')
@section('title','Sửa giá trị thuộc tính')
@section('content')
<h3>Sửa giá trị thuộc tính</h3>
<form method="POST" action="{{ route('admin.attribute_values.update', $value->id) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label">Thuộc tính</label>
        <select name="attribute_id" class="form-select" required>
            @foreach($attributes as $a)
                <option value="{{ $a->id }}" {{ $a->id==$value->attribute_id ? 'selected' : '' }}>{{ $a->name }}</option>
            @endforeach
        </select>
        @error('attribute_id')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Giá trị</label>
        <input type="text" name="value" class="form-control" value="{{ old('value', $value->value) }}" required>
        @error('value')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <button class="btn btn-primary">Lưu</button>
    <a href="{{ route('admin.attribute_values.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection
