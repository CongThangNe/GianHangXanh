@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="container mx-auto px-4 py-8 md:py-12 max-w-6xl">

    {{-- Breadcrumb nhỏ trong trang hồ sơ --}}
    <div class="mb-6 text-sm text-gray-600">
        <a href="{{ route('profile.show') }}" class="hover:text-green-700 font-medium">
            Hồ sơ cá nhân
        </a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-green-700 font-semibold">Đơn hàng của tôi</span>
    </div>

    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
        Đơn hàng của tôi
    </h1>

    {{-- Thông báo --}}
    @if (session('success'))
        <div class="mb-4 rounded border border-green-400 bg-green-50 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded border border-red-400 bg-red-50 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    {{-- Bộ lọc trạng thái --}}
    <div class="mb-6 flex flex-wrap gap-2">
        @php
            $statusFilter = $statusFilter ?? request('status', 'all');

            $statusOptions = [
                'all'       => 'Tất cả',
                'pending'   => 'Chờ xác nhận',
                'confirmed' => 'Đã xác nhận',
                'preparing' => 'Đang chuẩn bị',
                'shipping'  => 'Đang giao hàng',
                'delivered' => 'Đã giao',
                'cancelled' => 'Đã hủy',
            ];
        @endphp

        @foreach($statusOptions as $key => $label)
            @php
                $isActive = $statusFilter === $key;
            @endphp
            <a href="{{ route('user.orders.index', ['status' => $key]) }}"
               class="px-3 py-1 rounded-full text-sm font-medium border
                      {{ $isActive
                           ? 'bg-green-600 border-green-600 text-white'
                           : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Danh sách đơn --}}
    @if (empty($orders) || $orders->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-dashed border-gray-300 p-8 text-center">
            <p class="text-gray-600 mb-2">Bạn chưa có đơn hàng nào.</p>
            <a href="{{ route('home') }}"
               class="inline-flex items-center mt-2 px-4 py-2 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700">
                Bắt đầu mua sắm
            </a>
        </div>
    @else
        <div class="space-y-5">
            @foreach ($orders as $order)
                @php
                    // Map trạng thái
                    $statusDisplay = [
                        'pending'   => ['Chờ xác nhận', 'bg-yellow-100 text-yellow-800'],
                        'confirmed' => ['Đã xác nhận', 'bg-blue-100 text-blue-800'],
                        'preparing' => ['Đang chuẩn bị hàng', 'bg-blue-100 text-blue-800'],
                        'shipping'  => ['Đang vận chuyển', 'bg-indigo-100 text-indigo-800'],
                        'delivered' => ['Đã giao thành công', 'bg-green-100 text-green-800'],
                        'cancelled' => ['Đã hủy', 'bg-red-100 text-red-800'],
                        'paid'      => ['Đã thanh toán', 'bg-green-100 text-green-800'],
                    ][$order->status] ?? ['Không xác định', 'bg-gray-100 text-gray-800'];

                    $totalQuantity = $order->details->sum('quantity');
                    $firstDetail   = $order->details->first();
                    $firstProduct  = $firstDetail?->variant?->product;
                @endphp

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 md:px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-gray-100">
                        <div>
                            <p class="text-xs uppercase font-medium text-gray-500">Mã đơn hàng</p>
                            <span class="text-lg font-bold text-green-700">{{ $order->order_code }}</span>
                            <p class="text-xs text-gray-500 mt-1">
                                Ngày đặt: {{ $order->created_at?->format('d/m/Y H:i') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusDisplay[1] }}">
                                {{ $statusDisplay[0] }}
                            </span>

                            <a href="{{ route('user.orders.show', $order->id) }}"
                               class="text-sm font-semibold bg-green-600 text-white px-3 py-1.5 rounded-lg hover:bg-green-700 shadow-sm">
                                Chi tiết
                            </a>
                        </div>
                    </div>

                    {{-- Nội dung tóm tắt đơn --}}
                    <div class="p-4 md:p-6">
                        <div class="flex flex-col md:flex-row gap-4 md:gap-6">
                            {{-- Sản phẩm đầu tiên --}}
                            <div class="flex-1 flex items-center gap-4">
                                @if ($firstProduct)
                                    <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-50 flex-shrink-0">
                                        @if ($firstProduct->image)
                                            <img src="{{ asset('storage/' . $firstProduct->image) }}"
                                                 alt="{{ $firstProduct->name }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                                                Không có ảnh
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="font-semibold text-gray-900">
                                            {{ $firstProduct->name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            @if ($totalQuantity > 1)
                                                + {{ $totalQuantity - 1 }} sản phẩm khác
                                            @else
                                                1 sản phẩm
                                            @endif
                                        </p>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">Không có dữ liệu sản phẩm.</p>
                                @endif
                            </div>

                            {{-- Tổng tiền --}}
                            <div class="md:text-right">
                                <p class="text-xs uppercase font-medium text-gray-500 mb-1">Tổng cộng</p>
                                <p class="text-2xl font-extrabold text-red-600">
                                    {{ number_format($order->total) }}₫
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Phương thức: {{ strtoupper($order->payment_method) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- PHÂN TRANG --}}
        <div class="mt-8">
            {{ $orders->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
