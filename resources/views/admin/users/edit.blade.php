@extends('layouts.admin')
@section('title','Cập nhật vai trò')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">← Quay lại</a>
            </div>

            <h5 class="mb-3">Tài khoản: <strong>{{ $user->name }}</strong> <span class="text-muted">({{ $user->email }})</span></h5>

            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label class="form-label form-required">Vai trò</label>
                    <select class="form-select" name="role" required>
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" @selected(old('role', $user->role) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hiện tại bạn chỉ cần 3 role: Admin, Khách hàng, Nhân viên (chưa cần chức năng phân quyền).</small>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-success">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
