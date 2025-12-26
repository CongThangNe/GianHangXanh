@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-edit text-warning"></i> Chỉnh sửa tin tức
        </h4>
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('admin.news.update', $news->id) }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Tiêu đề -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-heading"></i> Tiêu đề
                    </label>
                    <input type="text" name="title"
                           value="{{ old('title', $news->title) }}"
                           class="form-control">
                </div>

                <!-- Mô tả ngắn -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-align-left"></i> Mô tả ngắn
                    </label>
                    <textarea name="short_description" rows="3"
                              class="form-control">{{ old('short_description', $news->short_description) }}</textarea>
                </div>

                <!-- Nội dung -->
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-file-alt"></i> Nội dung chi tiết
                    </label>
                    <textarea name="content" rows="6"
                              class="form-control editor">{{ old('content', $news->content) }}</textarea>
                </div>

                <div class="row">
                    <!-- Ảnh -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-image"></i> Ảnh đại diện
                        </label>
                        <input type="file" name="image" class="form-control">

                        @if($news->image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$news->image) }}"
                                     class="img-thumbnail"
                                     style="max-height: 120px">
                            </div>
                        @endif
                    </div>

                    <!-- Trạng thái -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-toggle-on"></i> Trạng thái
                        </label>
                        <select name="status" class="form-select">
                            <option value="1" {{ $news->status == 1 ? 'selected' : '' }}>Hiển thị</option>
                            <option value="0" {{ $news->status == 0 ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>
                </div>

                <!-- Button -->
                <div class="text-end">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Cập nhật tin tức
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
