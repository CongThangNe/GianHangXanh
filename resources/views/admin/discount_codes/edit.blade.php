@extends('layouts.admin')
@section('title', 'Chỉnh sửa mã giảm giá')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Chỉnh sửa mã giảm giá</h3>

    <form method="POST" action="{{ route('admin.discount-codes.update', $discountCode->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Mã Code *</label>
            <input name="code" class="form-control" required value="{{ old('code', $discountCode->code) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Loại Giảm Giá *</label><br>
            <div class="form-check form-check-inline">
                <input type="radio" id="typePercent" name="type" value="percent"
                       class="form-check-input"
                       {{ $discountCode->discount_percent > 0 ? 'checked' : '' }}>
                <label for="typePercent" class="form-check-label">Theo %</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" id="typeValue" name="type" value="value"
                       class="form-check-input"
                       {{ $discountCode->discount_value > 0 ? 'checked' : '' }}>
                <label for="typeValue" class="form-check-label">Theo VNĐ</label>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá trị giảm *</label>
            <input type="number" name="value" class="form-control"
                   value="{{ old('value', $discountCode->discount_percent ?: $discountCode->discount_value) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày bắt đầu</label>
            <input type="date" name="starts_at" class="form-control"
                   value="{{ old('starts_at', $discountCode->starts_at ? $discountCode->starts_at->format('Y-m-d') : '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày hết hạn</label>
            <input type="date" name="expires_at" class="form-control"
                   value="{{ old('expires_at', $discountCode->expires_at ? $discountCode->expires_at->format('Y-m-d') : '') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Số lần sử dụng tối đa</label>
            <input type="number" name="max_uses" class="form-control"
                   value="{{ old('max_uses', $discountCode->max_uses) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Giá trị giảm tối đa (nếu theo %)</label>
            <input type="number" name="max_discount_value" class="form-control"
                   value="{{ old('max_discount_value', $discountCode->max_discount_value) }}">
        </div>

        <button class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.discount-codes.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
