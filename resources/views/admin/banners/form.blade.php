@csrf
<div class="mb-3">
    <label>Tiêu đề</label>
    <input type="text" name="title" value="{{ old('title', $banner->title ?? '') }}" class="form-control">
</div>

{{-- <div class="mb-3">
    <label>Đường dẫn</label>
    <input type="text" name="link" value="{{ old('link', $banner->link ?? '') }}" class="form-control">
</div> --}}

<div class="mb-3">
    <label>Ảnh Banner</label>
    <input type="file" name="image" class="form-control">
    @if (!empty($banner->image))
        <img src="{{ Storage::url($banner->image) }}"
        style="max-height:100px;max-width:100%;object-fit:contain;margin-top:8px;">
    @endif
</div>

<div class="mb-3">
    <label>Thứ tự</label>
    <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}"
        class="form-control">
</div>

<div class="mb-3">
    <label>Trạng thái</label>
    <select name="status" class="form-control">
        <option value="1" {{ old('status', $banner->status ?? 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
        <option value="0" {{ old('status', $banner->status ?? 1) == 0 ? 'selected' : '' }}>Không hoạt động</option>
    </select>
</div>

<button class="btn btn-primary" type="submit">Lưu</button>
