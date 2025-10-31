@extends('layouts.admin')
@section('title','Dashboard')

@section('content')
<div class="container-fluid p-4">
    <h4>Dashboard</h4>
    <p>Tổng quan hệ thống</p>

    <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="card p-3">Người dùng: {{ $userCount ?? 0 }}</div></div>
        <div class="col-md-3"><div class="card p-3">Doanh thu: {{ number_format($revenue ?? 0,0,',','.') }}₫</div></div>
        <div class="col-md-3"><div class="card p-3">Đơn hàng: {{ $orderCount ?? 0 }}</div></div>
        <div class="col-md-3"><div class="card p-3">Hàng tồn: {{ $stockCount ?? 0 }}</div></div>
    </div>

    <h5>Danh sách người dùng</h5>
    <table class="table table-hover">
        <thead><tr><th>#</th><th>Tên</th><th>Email</th><th>Vai trò</th><th>Trạng thái</th></tr></thead>
        <tbody>
            @foreach($users ?? [] as $i => $user)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
