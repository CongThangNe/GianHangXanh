@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16 text-center">
    <div class="bg-white p-8 rounded-xl shadow-lg max-w-lg mx-auto border border-green-100">
        {{-- Icon Check xanh --}}
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
            <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h2 class="text-3xl font-bold text-gray-800 mb-2">Thanh toán thành công!</h2>
        <p class="text-gray-600 mb-8">Cảm ơn bạn đã mua sắm tại Gian Hàng Xanh.</p>

        <div class="bg-gray-50 rounded-lg p-4 mb-8 text-left text-sm">
            <div class="flex justify-between mb-2">
                <span class="text-gray-500">Mã đơn hàng:</span>
                <span class="font-bold text-gray-800">{{ $order_code }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-gray-500">Số tiền:</span>
                <span class="font-bold text-green-600">{{ number_format($amount) }} VND</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-gray-500">Ngân hàng:</span>
                <span class="font-bold text-gray-800">{{ $bank }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Thời gian:</span>
                <span class="text-gray-800">{{ \Carbon\Carbon::createFromFormat('YmdHis', $time)->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>

        <a href="{{ route('home') }}" class="inline-block bg-green-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-green-700 transition">
            Tiếp tục mua sắm
        </a>
    </div>
</div>
@endsection