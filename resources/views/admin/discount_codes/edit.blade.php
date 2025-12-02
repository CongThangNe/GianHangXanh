@extends('layouts.admin')
@section('title', 'Chỉnh sửa mã giảm giá')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Chỉnh sửa mã giảm giá</h3>

    {{-- Hiển thị lỗi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.discount-codes.update', $discountCode->id) }}">
        @csrf
        @method('PUT')

        {{-- Mã code --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Mã code *</label>
            <input name="code" type="text" class="form-control" required
                   value="{{ old('code', $discountCode->code) }}">
            @error('code') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Loại giảm giá --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Loại giảm giá *</label>
            <div>
                <div class="form-check form-check-inline">
                    <input type="radio" id="typePercent" name="type" value="percent"
                           class="form-check-input"
                           {{ old('type', $discountCode->discount_percent > 0 ? 'percent' : '') === 'percent' ? 'checked' : '' }}>
                    <label class="form-check-label" for="typePercent">Giảm theo %</label>
                </div>

                <div class="form-check form-check-inline">
                    <input type="radio" id="typeValue" name="type" value="value"
                           class="form-check-input"
                           {{ old('type', $discountCode->discount_value > 0 ? 'value' : '') === 'value' ? 'checked' : '' }}>
                    <label class="form-check-label" for="typeValue">Giảm trực tiếp (VNĐ)</label>
                </div>
            </div>
            @error('type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Giá trị giảm --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Giá trị giảm *</label>
            <input name="value" type="number" step="0.01" class="form-control" required
                    value="{{ old('value', $discountCode->discount_percent ?: $discountCode->discount_value) }}">
            <small class="text-muted">Nếu là %, nhập 1–100. Nếu là VNĐ, nhập số tiền trực tiếp.</small>
            @error('value') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Giá trị giảm tối đa cho % --}}
        <div class="mb-3" id="maxDiscountBox"
            style="{{ old('type', $discountCode->discount_percent > 0 ? 'percent' : 'value') === 'percent' ? '' : 'display:none;' }}">
            <label class="form-label fw-bold">Giá trị giảm tối đa (VNĐ)</label>
            <input name="max_discount_value" type="number" step="0.01" min="0" class="form-control"
                    value="{{ old('max_discount_value', $discountCode->max_discount_value) }}">
            <small class="text-muted">Áp dụng khi giảm theo %. Để trống nếu không giới hạn.</small>
            @error('max_discount_value') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Ngày bắt đầu --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Ngày bắt đầu *</label>
            <input name="starts_at" type="date" class="form-control"
                value="{{ old('starts_at', optional($discountCode->starts_at)->format('Y-m-d')) }}">
            @error('starts_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Ngày hết hạn --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Ngày hết hạn</label>
            <input name="expires_at" type="date" class="form-control"
                value="{{ old('expires_at', optional($discountCode->expires_at)->format('Y-m-d')) }}">
            @error('expires_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Số lần sử dụng tối đa --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Số lần sử dụng tối đa</label>
            <input name="max_uses" type="number" min="0" class="form-control"
                value="{{ old('max_uses', $discountCode->max_uses) }}">
            <small class="text-muted">Để trống hoặc nhập 0 nếu không giới hạn.</small>
            @error('max_uses') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.discount-codes.index') }}" class="btn btn-secondary">Quay lại</a>

    </form>
</div>

{{-- Script toggle giống trang create --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typePercent = document.getElementById('typePercent');
    const typeValue = document.getElementById('typeValue');
    const maxBox = document.getElementById('maxDiscountBox');

    function toggleMaxBox() {
        if (typePercent.checked) {
            maxBox.style.display = '';
        } else {
            maxBox.style.display = 'none';
        }
    }

    typePercent.addEventListener('change', toggleMaxBox);
    typeValue.addEventListener('change', toggleMaxBox);
});
</script>
@endsection
