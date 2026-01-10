@extends('layouts.admin')
@section('title', 'Thêm mã giảm giá')

@section('content')
<div class="container-fluid p-4">
    <h3 class="mb-4">Thêm mã giảm giá</h3>

    <form action="{{ route('admin.discount-codes.store') }}" method="POST" class="card p-4">
        @csrf

        {{-- Code --}}
        <div class="mb-3">
            <label class="form-label">Mã giảm giá</label>
            <input type="text"
                   name="code"
                   class="form-control"
                   value="{{ old('code') }}"
                   required>
        </div>

        {{-- Type --}}
        <div class="mb-3">
            <label class="form-label">Loại giảm</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input"
                           type="radio"
                           name="type"
                           id="typePercent"
                           value="percent"
                           {{ old('type', 'percent') === 'percent' ? 'checked' : '' }}>
                    <label class="form-check-label" for="typePercent">%</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input"
                           type="radio"
                           name="type"
                           id="typeValue"
                           value="value"
                           {{ old('type') === 'value' ? 'checked' : '' }}>
                    <label class="form-check-label" for="typeValue">VNĐ</label>
                </div>
            </div>
        </div>

        {{-- Value --}}
        <div class="mb-3">
            <label class="form-label">Giá trị giảm</label>
            <input type="number"
                   name="value"
                   class="form-control"
                   value="{{ old('value') }}"
                   required>
        </div>

        {{-- Max discount (only %) --}}
        <div class="mb-3" id="maxDiscountBox">
            <label class="form-label">Giảm tối đa (chỉ áp dụng cho %)</label>
            <input type="number"
                   name="max_discount_value"
                   class="form-control"
                   value="{{ old('max_discount_value') }}">
        </div>

        {{-- Max uses --}}
        <div class="mb-3">
            <label class="form-label">
                Số lượt có thể sử dụng 
            </label>
            <input type="number"
                   name="max_uses"
                   class="form-control"
                   value="{{ old('max_uses', 0) }}"
                   min="0">
        </div>

        {{-- Dates --}}
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Bắt đầu</label>
                <input type="datetime-local"
                       name="starts_at"
                       class="form-control"
                       value="{{ old('starts_at') }}">
            </div>
            <div class="col">
                <label class="form-label">Hết hạn</label>
                <input type="datetime-local"
                       name="expires_at"
                       class="form-control"
                       value="{{ old('expires_at') }}">
            </div>
        </div>

        {{-- Active --}}
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input"
                       type="checkbox"
                       name="active"
                       value="1"
                       {{ old('active', true) ? 'checked' : '' }}>
                <label class="form-check-label">Kích hoạt</label>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex gap-2">
            <button class="btn btn-success">Lưu</button>
            <a href="{{ route('admin.discount-codes.index') }}"
               class="btn btn-secondary">
                Quay lại
            </a>
        </div>
    </form>
</div>

{{-- JS toggle --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const percent = document.getElementById('typePercent');
    const value = document.getElementById('typeValue');
    const box = document.getElementById('maxDiscountBox');

    function toggle() {
        box.style.display = percent.checked ? 'block' : 'none';
    }

    percent.addEventListener('change', toggle);
    value.addEventListener('change', toggle);
    toggle();
});
</script>
@endsection
