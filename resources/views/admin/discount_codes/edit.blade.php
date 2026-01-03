@extends('layouts.admin')
@section('title', 'Sửa mã giảm giá')

@section('content')
<form method="POST" action="{{ route('admin.discount-codes.update', $discountCode) }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Mã</label>
        <input name="code" class="form-control" value="{{ old('code', $discountCode->code) }}">
    </div>

    <div class="mb-3">
        <label>Loại</label>
        <select name="type" id="type" class="form-select">
            <option value="percent" @selected($discountCode->type === 'percent')>%</option>
            <option value="value" @selected($discountCode->type === 'value')>VND</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Giá trị</label>
        <input type="number" name="value" class="form-control"
               value="{{ old('value', $discountCode->value) }}">
    </div>

    <div class="mb-3" id="maxBox">
        <label>Giảm tối đa</label>
        <input type="number" name="max_discount_value" class="form-control"
               value="{{ old('max_discount_value', $discountCode->max_discount_value) }}">
    </div>

    <div class="mb-3">
        <label>Lượt dùng (0 = không giới hạn)</label>
        <input type="number" name="max_uses" class="form-control"
               value="{{ old('max_uses', $discountCode->max_uses) }}">
    </div>

    <div class="row mb-3">
        <div class="col">
            <label>Bắt đầu</label>
            <input type="date" name="starts_at" class="form-control"
                   value="{{ optional($discountCode->starts_at)->format('Y-m-d') }}">
        </div>
        <div class="col">
            <label>Hết hạn</label>
            <input type="date" name="expires_at" class="form-control"
                   value="{{ optional($discountCode->expires_at)->format('Y-m-d') }}">
        </div>
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="active" class="form-check-input"
               @checked($discountCode->active)>
        <label class="form-check-label">Kích hoạt</label>
    </div>

    <button class="btn btn-primary">Cập nhật</button>
    <a href="{{ route('admin.discount-codes.index') }}" class="btn btn-secondary">Quay lại</a>
</form>

<script>
    const type = document.getElementById('type');
    const maxBox = document.getElementById('maxBox');

    function toggle() {
        maxBox.style.display = type.value === 'percent' ? '' : 'none';
    }

    toggle();
    type.addEventListener('change', toggle);
</script>
@endsection
