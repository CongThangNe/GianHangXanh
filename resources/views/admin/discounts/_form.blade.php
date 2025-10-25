@csrf
<div class="row">
    <div class="col-md-6 mb-3">
        <label>Mã giảm giá</label>
        <input type="text" name="code" value="{{ old('code', $discount->code ?? '') }}" class="form-control" required>
        @error('code')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
    <div class="col-md-3 mb-3">
        <label>Phần trăm (%)</label>
        <input type="number" name="percentage" value="{{ old('percentage', $discount->percentage ?? '') }}" step="0.01" class="form-control">
        @error('percentage')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
    <div class="col-md-3 mb-3">
        <label>Giảm tiền (VNĐ)</label>
        <input type="number" name="fixed_amount" value="{{ old('fixed_amount', $discount->fixed_amount ?? '') }}" step="0.01" class="form-control">
        @error('fixed_amount')<small class="text-danger">{{ $message }}</small>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Số lần sử dụng</label>
        <input type="number" name="usage_limit" value="{{ old('usage_limit', $discount->usage_limit ?? '') }}" class="form-control">
    </div>
    <div class="col-md-4 mb-3">
        <label>Bắt đầu</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($discount->starts_at)->format('Y-m-d\TH:i')) }}" class="form-control">
    </div>
    <div class="col-md-4 mb-3">
        <label>Kết thúc</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($discount->ends_at)->format('Y-m-d\TH:i')) }}" class="form-control">
    </div>
</div>

<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" name="is_active" value="1"
        {{ old('is_active', $discount->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label">Hoạt động</label>
</div>

<button class="btn btn-primary">💾 Lưu</button>
<a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">⬅ Trở lại</a>
