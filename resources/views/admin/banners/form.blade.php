@csrf
<div class="mb-3">
    <label>Title</label>
    <input type="text" name="title" value="{{ old('title', $banner->title ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label>Link</label>
    <input type="text" name="link" value="{{ old('link', $banner->link ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label>Image</label>
    <input type="file" name="image" class="form-control">
    @if(!empty($banner->image))
        <img src="{{ Storage::url($banner->image) }}" style="max-height:100px;margin-top:8px;">
    @endif
</div>

<div class="mb-3">
    <label>Sort Order</label>
    <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="form-control">
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="1" {{ old('status', $banner->status ?? 1) == 1 ? 'selected':'' }}>Active</option>
        <option value="0" {{ old('status', $banner->status ?? 1) == 0 ? 'selected':'' }}>Inactive</option>
    </select>
</div>

<button class="btn btn-primary" type="submit">Lưu</button>
