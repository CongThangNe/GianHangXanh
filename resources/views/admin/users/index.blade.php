@extends('layouts.admin')
@section('title','Quản lý tài khoản')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form class="row g-2 mb-3" method="GET" action="{{ route('admin.users.index') }}">
                <div class="col-md-6">
                    <input class="form-control" name="q" value="{{ $q ?? '' }}" placeholder="Tìm theo tên hoặc email..." />
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-success w-100" type="submit">Tìm</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width:70px">#</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th style="width:180px">Vai trò</th>
                            <th style="width:140px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $user->role_label }}</span>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.edit', $user) }}">Sửa role</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Chưa có tài khoản.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
