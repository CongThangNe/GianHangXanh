
@extends('layouts.app')
=======
@extends('layouts.admin')

@section('title', 'Sửa mã giảm giá')
@section('content')
<h3>Sửa Mã Giảm Giá: {{ $discountCode->code }}</h3>
<form method="POST" action="{{ route('admin.discount-codes.update', $discountCode->id) }}">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label class="form-label">Mã Code *</label>
        <input name="code" class="form-control" required value="{{ old('code', $discountCode->code) }}">
        @error('code') <div class="text-danger mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Loại Giảm Giá *</label>
        <div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="type" id="typePercentEdit" value="percent" 
                    {{ old('type', $discountCode->type) == 'percent' ? 'checked' : '' }}>
                <label class="form-check-label" for="typePercentEdit">Giảm theo % (0 < value < 100)</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="type" id="typeValueEdit" value="value" 
                    {{ old('type', $discountCode->type) == 'value' ? 'checked' : '' }}>
                <label class="form-check-label" for="typeValueEdit">Giảm trực tiếp (value > 0)</label>
            </div>
            @error('type') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Giá trị Giảm *</label>
        <input name="value" type="number" step="0.01" min="1" class="form-control" required 
               value="{{ old('value', $discountCode->value) }}">
        @error('value') <div class="text-danger mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Ngày Hết Hạn</label>
        <input name="expires_at" type="date" class="form-control" 
               value="{{ old('expires_at', $discountCode->expires_at ? $discountCode->expires_at->format('Y-m-d') : '') }}">
        @error('expires_at') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        <small class="text-muted">Để trống nếu không có thời hạn.</small>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Số lần sử dụng tối đa</label>
        <input name="max_uses" type="number" min="1" class="form-control" value="{{ old('max_uses', $discountCode->max_uses) }}">
        @error('max_uses') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        <small class="text-muted">Để trống hoặc 0 nếu không giới hạn. Đã sử dụng: **{{ $discountCode->used_count }}** lần.</small>
    </div>

    <button class="btn btn-primary">Cập nhật</button>
    <a href="{{ route('admin.discount-codes.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection