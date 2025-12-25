@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-newspaper text-primary"></i> Thêm tin tức mới
        </h4>
        <a href="{{ route('news.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Tiêu đề -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-heading"></i> Tiêu đề
                    </label>
                    <input type="text" name="title" class="form-control"
                           placeholder="Nhập tiêu đề tin tức">
                </div>

                <!-- Mô tả ngắn -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-align-left"></i> Mô tả ngắn
                    </label>
                    <textarea name="short_description" rows="3"
                              class="form-control"
                              placeholder="Mô tả ngắn hiển thị ngoài trang danh sách"></textarea>
                </div>

                <!-- Nội dung -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-file-alt"></i> Nội dung chi tiết
                    </label>
                    <textarea name="content" rows="6"
                              class="form-control editor"></textarea>
                </div>

                <div class="row">
                    <!-- Ảnh -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-image"></i> Ảnh đại diện
                        </label>
                        <input type="file" name="image" class="form-control">
                    </div>

                    <!-- Trạng thái -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-toggle-on"></i> Trạng thái
                        </label>
                        <select name="status" class="form-select">
                            <option value="1">Hiển thị</option>
                            <option value="0">Ẩn</option>
                        </select>
                    </div>
                </div>

                <!-- Button -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu tin tức
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
