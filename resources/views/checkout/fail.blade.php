@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16 text-center">
    <div class="bg-white p-8 rounded-xl shadow-lg max-w-lg mx-auto border border-red-100">
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-6">
            <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Thanh toán thất bại</h2>
        <p class="text-red-600 mb-8">{{ $msg ?? 'Có lỗi xảy ra trong quá trình thanh toán.' }}</p>
        
        <a href="{{ route('checkout.index') }}" class="inline-block bg-gray-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-gray-700 transition">
            Thử lại
        </a>
    </div>
</div>
@endsection