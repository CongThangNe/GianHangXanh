@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container mx-auto px-4 py-8 md:py-12 max-w-5xl">

    {{-- Breadcrumb --}}
    <div class="mb-6 text-sm text-gray-600">
        <a href="{{ route('profile.show') }}" class="hover:text-green-700 font-medium">
            Hồ sơ cá nhân
        </a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('user.orders.index') }}" class="hover:text-green-700 font-medium">
            Đơn hàng của tôi
        </a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-green-700 font-semibold">Chi tiết đơn #{{ $order->order_code }}</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Header --}}
        <div class="px-4 md:px-6 py-4 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-900">
                    Đơn hàng #{{ $order->order_code }}
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Ngày đặt: {{ $order->created_at?->format('d/m/Y H:i') }}
                </p>
            </div>

            @php
                $statusDisplay = [
                    'pending'   => ['Chờ xác nhận', 'bg-yellow-100 text-yellow-800'],
                    'confirmed' => ['Đã xác nhận', 'bg-blue-100 text-blue-800'],
                    'preparing' => ['Đang chuẩn bị hàng', 'bg-blue-100 text-blue-800'],
                    'shipping'  => ['Đang vận chuyển', 'bg-indigo-100 text-indigo-800'],
                    'delivered' => ['Đã giao thành công', 'bg-green-100 text-green-800'],
                    'cancelled' => ['Đã hủy', 'bg-red-100 text-red-800'],
                ][$order->delivery_status] ?? ['Đã hủy', 'bg-red-100 text-gray-800'];
            @endphp

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $statusDisplay[1] }}">
                    {{ $statusDisplay[0] }}
                </span>
                @php
                    $pay = [
                        'unpaid' => ['Chưa thanh toán', 'bg-gray-100 text-gray-800'],
                        'paid'   => ['Đã thanh toán',  'bg-green-100 text-green-800'],
                    ][$order->payment_status] ?? ['Đã hủy', 'bg-red-100 text-gray-800'];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $pay[1] }}">
                    {{ $pay[0] }}
                </span>
            </div>
        </div>

        {{-- Thông tin khách & giao hàng --}}
        <div class="px-4 md:px-6 py-4 md:py-6 grid md:grid-cols-2 gap-6 border-b border-gray-100">
            <div>
                <h2 class="text-sm font-semibold text-gray-700 mb-2 uppercase">Thông tin khách hàng</h2>
                <p class="text-sm text-gray-800">{{ $order->customer_name }}</p>
                <p class="text-sm text-gray-800 mt-1">{{ $order->customer_phone }}</p>
                <p class="text-sm text-gray-600 mt-1">{{ $order->customer_address }}</p>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-700 mb-2 uppercase">Thanh toán</h2>
                <p class="text-sm text-gray-800">
                    Phương thức: <span class="font-semibold">{{ strtoupper($order->payment_method) }}</span>
                </p>
                <p class="text-sm text-gray-800 mt-1">
                    Tổng thanh toán: <span class="font-bold text-red-600">{{ number_format($order->total) }}₫</span>
                </p>
                @if ($discountAmount > 0)
                    <p class="text-sm text-gray-600 mt-1">
                        Đã giảm: {{ number_format($discountAmount) }}₫
                    </p>
                @endif
                @if ($order->note)
                    <p class="text-sm text-gray-600 mt-2">
                        Ghi chú: {{ $order->note }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Bảng sản phẩm --}}
        <div class="px-4 md:px-6 py-4 md:py-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-3 uppercase">
                Sản phẩm trong đơn
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="py-2 px-3 text-left font-semibold text-gray-700">Sản phẩm</th>
                            <th class="py-2 px-3 text-center font-semibold text-gray-700">Số lượng</th>
                            <th class="py-2 px-3 text-right font-semibold text-gray-700">Đơn giá</th>
                            <th class="py-2 px-3 text-right font-semibold text-gray-700">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->details as $detail)
                            @php
                                $variant = $detail->variant;
                                $product = $variant?->product;
                                $lineTotal = (int)$detail->price * (int)$detail->quantity;
                            @endphp
                            <tr class="border-b border-gray-100">
                                <td class="py-2 px-3">
                                    <div class="flex items-center gap-3">
                                        @if ($product && $product->image)
                                            <div class="w-12 h-12 rounded-md overflow-hidden bg-gray-50 flex-shrink-0">
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                     alt="{{ $product->name }}"
                                                     class="w-full h-full object-cover">
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                {{ $product?->name ?? 'Sản phẩm' }}
                                            </p>
                                            @if ($variant && $variant->values->isNotEmpty())
                                                <p class="text-xs text-gray-500 mt-1">
                                                    @foreach ($variant->values as $value)
                                                        <span>{{ $value->attribute->name ?? '' }}: {{ $value->value }}</span>
                                                        @if (!$loop->last), @endif
                                                    @endforeach
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    {{ $detail->quantity }}
                                </td>
                                <td class="py-2 px-3 text-right">
                                    {{ number_format($detail->price) }}₫
                                </td>
                                <td class="py-2 px-3 text-right font-semibold">
                                    {{ number_format($lineTotal) }}₫
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="py-3 px-3 text-right text-sm text-gray-600">
                                Tổng tiền hàng
                            </td>
                            <td class="py-3 px-3 text-right text-sm font-semibold">
                                {{ number_format($subtotal) }}₫
                            </td>
                        </tr>
                        @if ($discountAmount > 0)
                            <tr>
                                <td colspan="3" class="py-1 px-3 text-right text-sm text-gray-600">
                                    Giảm giá
                                </td>
                                <td class="py-1 px-3 text-right text-sm font-semibold text-green-700">
                                    -{{ number_format($discountAmount) }}₫
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="py-3 px-3 text-right text-sm text-gray-800 font-semibold">
                                Tổng thanh toán
                            </td>
                            <td class="py-3 px-3 text-right text-lg font-extrabold text-red-600">
                                {{ number_format($order->total) }}₫
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('user.orders.index') }}"
           class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200">
            ← Quay lại danh sách đơn
        </a>
        @if($order->delivery_status === 'pending')
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 max-w-md">
            <p class="text-sm text-yellow-800 font-medium mb-3">
                Bạn có thể hủy đơn hàng này vì chưa được xác nhận.
            </p>
            
            <form action="{{ route('orders.cancel', $order->order_code) }}" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-5 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition shadow-sm whitespace-nowrap">
                    Hủy đơn hàng
                </button>
            </form>
        </div>
    @endif
    </div>
</div>
@endsection
