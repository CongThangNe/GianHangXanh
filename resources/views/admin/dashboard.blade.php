@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid p-4">
        <h4 class="fw-bold">Dashboard</h4>
        <p class="text-muted">Tổng quan hệ thống bán hàng</p>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-primary text-white">
                    <div class="small opacity-75">Người dùng</div>
                    <div class="h4 mb-0 fw-bold">{{ number_format($userCount) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-success text-white">
                    <div class="small opacity-75">Doanh thu (Đã thanh toán)</div>
                    <div class="h4 mb-0 fw-bold">{{ number_format($revenue, 0, ',', '.') }}₫</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-warning text-dark">
                    <div class="small opacity-75">Tổng đơn hàng</div>
                    <div class="h4 mb-0 fw-bold">{{ number_format($orderCount) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-danger text-white">
                    <div class="small opacity-75">Hàng tồn kho</div>
                    <div class="h4 mb-0 fw-bold">{{ number_format($stockCount) }}</div>
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
                                        <th style="width: 50px">STT</th>
                                        <th>Sản phẩm</th>
                                        <th class="text-end">Giá bán</th>
                                        <th class="text-center">Lượt bán</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topSellingProducts as $index => $item)
                                        <tr>
                                            <td class="fw-bold">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $item->image_url ?? asset('images/no-image.png') }}"
                                                         alt="{{ $item->name }}" 
                                                         class="rounded border me-3" 
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
                                            <td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu bán hàng</td>
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
                                    <span class="badge {{ $user->is_active ? 'bg-light text-success' : 'bg-light text-danger' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer bg-white text-center">
                        <a href="#" class="btn btn-sm btn-link text-decoration-none">Xem tất cả người dùng</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection