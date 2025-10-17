@extends('layouts.app')
@section('title','Danh mục')
@section('content')
<div class="container">
    <h3>Danh mục</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Thông tin danh mục</h5>
            <p><strong>ID:</strong> {{ $category->id }}</p>
            <p><strong>Tên:</strong> {{ $category->name }}</p>
            <p><strong>Ngày tạo:</strong> {{ $category->created_at }}</p>
            <p><strong>Ngày cập nhật:</strong> {{ $category->updated_at }}</p>

            <button id="editToggle" class="btn btn-primary">Sửa danh mục</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>

    <div id="editForm" class="card" style="display:none;">
        <div class="card-body">
            <h5 class="card-title">Sửa danh mục</h5>
            <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Tên</label>
                    <input name="name" class="form-control" required value="{{ old('name', $category->name) }}">
                    @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <button class="btn btn-primary">Lưu</button>
                <button type="button" id="cancelEdit" class="btn btn-secondary">Hủy</button>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.getElementById('editToggle').addEventListener('click', function () {
    document.getElementById('editForm').style.display = 'block';
    window.scrollTo(0, document.getElementById('editForm').offsetTop);
});
document.getElementById('cancelEdit').addEventListener('click', function () {
    document.getElementById('editForm').style.display = 'none';
});
</script>
@endsection

@endsection