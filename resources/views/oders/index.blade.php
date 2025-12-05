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

    <h2 class="text-3xl font-extrabold text-gray-800 mb-8 border-b pb-3">
        Đơn hàng của tôi
    </h2>

    {{-- PHẦN LỌC TRẠNG THÁI --}}
    @php
        $currentStatus = $statusFilter ?? 'all';
        $statuses = [
            'all' => 'Tất cả',
            'pending' => 'Chờ xác nhận',
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang vận chuyển',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
        ];
    @endphp

    <div class="flex flex-wrap gap-2 md:gap-4 mb-8">
        @foreach ($statuses as $key => $label)
            <a href="{{ route('user.orders.index', ['status' => $key]) }}"
               class="px-4 py-2 text-sm font-semibold rounded-full transition duration-150
               {{ $currentStatus === $key ? 'bg-green-700 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-green-100 hover:text-green-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- DANH SÁCH ĐƠN HÀNG --}}
    @if ($orders->isEmpty())
        <div class="bg-gray-50 border border-gray-200 text-gray-600 p-8 rounded-lg text-center">
            <span class="text-4xl block mb-3">📦</span>
            <p class="text-lg font-semibold">Bạn chưa có đơn hàng nào.</p>
            <a href="{{ route('home') }}" class="mt-4 inline-block text-green-700 hover:text-green-900 font-medium underline">
                Bắt đầu mua sắm ngay!
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($orders as $order)
                <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">

                    {{-- HEADER ĐƠN HÀNG --}}
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 md:p-6 bg-gray-50 border-b border-gray-200">
                        <div class="mb-2 sm:mb-0">
                            <p class="text-xs uppercase font-medium text-gray-500">Mã đơn hàng</p>
                            <span class="text-lg font-bold text-green-700">{{ $order->order_code }}</span>
                        </div>

                        <div class="flex items-center space-x-4">
                            @php
                                $statusDisplay = [
                                    'pending' => ['Chờ xác nhận', 'bg-yellow-100 text-yellow-800'],
                                    'processing' => ['Đang xử lý', 'bg-blue-100 text-blue-800'],
                                    'shipping' => ['Đang vận chuyển', 'bg-indigo-100 text-indigo-800'],
                                    'delivered' => ['Đã giao thành công', 'bg-green-100 text-green-800'],
                                    'cancelled' => ['Đã hủy', 'bg-red-100 text-red-800'],
                                ][$order->status] ?? ['Không xác định', 'bg-gray-100 text-gray-800'];
                            @endphp

                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusDisplay[1] }}">
                                {{ $statusDisplay[0] }}
                            </span>

                            <a href="#"
                               class="text-sm font-semibold bg-green-600 text-white hover:bg-green-700 px-3 py-1.5 rounded-lg transition duration-150 shadow-md">
                                Chi tiết
                            </a>
                        </div>
                    </div>

                    {{-- NỘI DUNG --}}
                    <div class="p-4 md:p-6">
                        @if (!empty($order->orderItems))
                            @php $firstItem = $order->orderItems[0]; @endphp

                            <div class="flex items-center space-x-4 mb-4">
                                <img src="https://placehold.co/80x80/22C55E/white?text=SP"
                                     alt="{{ $firstItem->product_name }}"
                                     class="w-16 h-16 object-cover rounded-lg border border-gray-100">

                                <div>
                                    <p class="font-semibold text-gray-900 line-clamp-1">{{ $firstItem->product_name }}</p>
                                    <p class="text-sm text-gray-500">
                                        SL: {{ $firstItem->quantity }}
                                        @if (count($order->orderItems) > 1)
                                            <span class="font-medium text-gray-700"> & +{{ count($order->orderItems) - 1 }} sản phẩm khác</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif

                        {{-- TỔNG TIỀN --}}
                        <div class="flex justify-end items-center pt-4 border-t border-gray-100">
                            <span class="text-lg font-bold text-gray-800 mr-2">Tổng cộng:</span>
                            <span class="text-2xl font-extrabold text-red-600">{{ number_format($order->total_amount) }}₫</span>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- PHÂN TRANG --}}
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif

</div>

@endsection
