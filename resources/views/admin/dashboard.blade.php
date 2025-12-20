@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid p-4">
        <h4 class="fw-bold">Dashboard</h4>
        <p class="text-muted">Tổng quan hệ thống bán hàng</p>

        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-primary">
                    <div class="inner">
                        <h3>{{ number_format($userCount) }}</h3>
                        <p>Người dùng</p>
                    </div>
                    <i class="small-box-icon bi bi-people-fill"></i>
                    <a href="{{ route('admin.users.index') }}"
                        class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                        Xem chi tiết <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-success">
                    <div class="inner">
                        <h3>{{ number_format($revenue, 0, ',', '.') }}<sup class="fs-6">₫</sup></h3>
                        <p>Doanh thu (Đã thanh toán)</p>
                    </div>
                    <i class="small-box-icon bi bi-currency-dollar"></i>
                    <a href="{{ route('admin.orders.index') }}"
                        class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                        Xem báo cáo <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($orderCount) }}</h3>
                        <p>Tổng đơn hàng</p>
                    </div>
                    <i class="small-box-icon bi bi-cart-fill"></i>
                    <a href="{{ route('admin.orders.index') }}"
                        class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                        Quản lý đơn <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($stockCount) }}</h3>
                        <p>Hàng tồn kho</p>
                    </div>
                    <i class="small-box-icon bi bi-box-seam-fill"></i>
                    <a href="{{ route('admin.products.index') }}"
                        class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                        Kiểm tra kho <i class="bi bi-arrow-right-circle"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold">🔥 Top sản phẩm bán chạy nhất</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px">ID</th>
                                        <th>Sản phẩm</th>
                                        <th class="text-end">Giá bán</th>
                                        <th class="text-center">Lượt bán</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topSellingProducts as $index => $item)
                                        <tr>
                                            <td class="fw-bold">{{ $item->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $item->image_url ?? asset('images/no-image.png') }}"
                                                        alt="{{ $item->name }}" class="rounded border me-3"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $item->name }}</div>
                                                        {{-- <small class="text-muted">ID: #{{ $item->id }}</small> --}}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end fw-semibold">
                                                {{ number_format($item->price, 0, ',', '.') }}₫
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success rounded-pill px-3">
                                                    {{ number_format($item->total_sold ?? 0) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu bán hàng
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold">Người dùng mới</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach ($users as $user)
                                <li class="list-group-item border-0 d-flex justify-content-between align-items-center py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle p-2 me-3">
                                            <i class="bi bi-person text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                    <td>
                                        <span class="badge bg-secondary">{{ $user->role_label }}</span>
                                    </td>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer bg-white text-center">
                        <a href="{{ route('admin.users.index') }}"
                            class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Xem tất cả người
                            dùng</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="col-lg-12 mt-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 fw-bold">Đơn hàng mới</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px">ID</th>
                                            <th>Mã đơn hàng</th>
                                            <th>Khách hàng</th>
                                            <th class="text-center">Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày đặt</th>
                                            <th class="text-center">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $o)
                                            <tr>
                                                <td class="fw-bold">#{{ $o->id }}</td>
                                                <td><code>{{ $o->order_code }}</code></td>
                                                <td>{{ $o->customer_name }}</td>
                                                <td class="text-danger fw-bold text-center">
                                                    {{ number_format($o->total, 0, ',', '.') }}₫
                                                </td>
                                                <td>
                                                    @php
                                                        $deliveryDisplay = [
                                                            'pending' => ['Chờ xác nhận', 'bg-warning text-dark'],
                                                            'confirmed' => ['Đã xác nhận', 'bg-primary'],
                                                            'preparing' => ['Đang chuẩn bị', 'bg-info text-dark'],
                                                            'shipping' => ['Đang vận chuyển', 'bg-info text-dark'],
                                                            'delivered' => ['Thành công', 'bg-success'],
                                                            'cancelled' => ['Đã hủy', 'bg-danger'],
                                                        ][$o->delivery_status] ?? ['Không xác định', 'bg-secondary'];
                                                    @endphp
                                                    <span class="badge {{ $deliveryDisplay[1] }}">
                                                        {{ $deliveryDisplay[0] }}
                                                    </span>
                                                </td>
                                                <td>{{ $o->created_at ? $o->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.orders.show', $o->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        Chi tiết
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">Chưa có đơn hàng nào
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
