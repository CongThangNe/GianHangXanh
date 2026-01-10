@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid p-4">

        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold mb-0">Dashboard</h4>
                    <p class="text-muted mb-0">Tổng quan hệ thống bán hàng</p>
                </div>

                <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex gap-2 align-items-end">
                    <div class="form-group">
                        <label class="small fw-bold">Từ ngày:</label>
                        <input type="date" name="start_date" class="form-control form-control-sm"
                            value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label class="small fw-bold">Đến ngày:</label>
                        <input type="date" name="end_date" class="form-control form-control-sm"
                            value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-filter"></i> Lọc
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </form>
            </div>

            <div class="row mb-4">
            </div>

            <div class="row mb-4">
                {{-- Admin mới thấy Người dùng + Doanh thu --}}
                @if (auth()->user()->role === 'admin')
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
                @endif

                <div class="col-lg-{{ auth()->user()->role === 'admin' ? '3' : '6' }} col-6">
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

                <div class="col-lg-{{ auth()->user()->role === 'admin' ? '3' : '6' }} col-6">
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
                <div class="col-lg-6">
                    
                        <div class="card border shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title mb-0 fw-bold">Doanh thu</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex">
                                    <p class="d-flex flex-column">
                                        <span class="fw-bold fs-5">{{ number_format($revenue, 0, ',', '.') }} ₫</span>
                                        <span>Doanh thu kỳ này</span>
                                    </p>
                                    {{-- <p class="ms-auto d-flex flex-column text-end">
                                        <span class="text-success"> <i class="bi bi-arrow-up"></i> 33.1% </span>
                                        <span class="text-secondary">Since Past Year</span>
                                    </p> --}}
                                </div>
                                <div class="position-relative mb-4">
                                    <div id="sales-chart"></div>
                                </div>
                            </div>
                        </div>
                    
                </div>

                <div class="col-lg-6">
                    <div class="card border shadow-sm mb-4">
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
                                                        @php
                                                            $img = $item->image
                                                                ? asset('storage/' . $item->image)
                                                                : asset('images/no-image.png');
                                                        @endphp

                                                        <img src="{{ $img }}" alt="{{ $item->name }}"
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
                                                <td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu bán
                                                    hàng
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if (auth()->user()->role === 'admin')
                    <div class="col-lg-6">
                        <div class="card border shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="card-title mb-0 fw-bold">Người dùng mới</h5>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    @foreach ($users as $user)
                                        <li
                                            class="list-group-item border-0 d-flex justify-content-between align-items-center py-3">
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
                                    class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Xem tất cả
                                    người
                                    dùng</a>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-lg-6">
                        <div class="card border shadow-sm mb-4">
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
                                                            ][$o->delivery_status] ?? [
                                                                'Không xác định',
                                                                'bg-secondary',
                                                            ];
                                                        @endphp
                                                        <span class="badge {{ $deliveryDisplay[1] }}">
                                                            {{ $deliveryDisplay[0] }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $o->created_at ? $o->created_at->format('d/m/Y H:i') : 'N/A' }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('admin.orders.show', $o->id) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            Chi tiết
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">Chưa có đơn
                                                        hàng nào
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

@push('scripts')
<script>
    const chartLabels = @json($chartLabels);
    const chartData = @json($chartData);

    const sales_chart_options = {
        series: [{
            name: "Doanh thu",
            data: chartData,
        }],
        chart: {
            height: 300,
            type: "bar",
            toolbar: { show: false },
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "55%",
                borderRadius: 5,
            },
        },
        colors: ["#2f8f3a"], 
        dataLabels: { enabled: false },
        xaxis: {
            categories: chartLabels, 
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return val === 0 ? "0" : val.toLocaleString('vi-VN');
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val.toLocaleString('vi-VN') + " ₫";
                },
            },
        },
        grid: {
            show: true,
            borderColor: '#f1f1f1',
            strokeDashArray: 4,
        }
    };

    const sales_chart = new ApexCharts(
        document.querySelector("#sales-chart"),
        sales_chart_options
    );
    sales_chart.render();
</script>
@endpush
