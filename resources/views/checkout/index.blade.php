@extends('layouts.app')

@section('title', 'Thanh Toán')

@section('content')
<div class="container mx-auto px-4 py-8 md:py-12 max-w-7xl">

    {{-- TIÊU ĐỀ --}}
    <!-- <h2 class="text-3xl md:text-4xl font-extrabold text-center text-green-700 mb-8 md:mb-12">
        Thanh Toán Đơn Hàng
    </h2> -->

    {{-- THÔNG BÁO LỖI --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- KIỂM TRA GIỎ HÀNG --}}
    @if(empty($cartItems) || $cartItems->isEmpty())
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-6 rounded-lg text-center font-semibold">
            <span class="text-4xl block mb-3">🛒</span>
            Giỏ hàng của bạn đang trống! Vui lòng thêm sản phẩm để tiến hành thanh toán.
        </div>
    @else
        <form id="checkout-form" method="POST" action="{{ route('checkout.process') }}">
            @csrf
            <div class="flex flex-col lg:flex-row gap-8">

                {{-- CỘT 1: THÔNG TIN & THANH TOÁN --}}
                <div class="w-full lg:w-7/12 order-2 lg:order-1">

                    {{-- THÔNG TIN GIAO HÀNG --}}
                    <div class="bg-white shadow-xl rounded-xl p-6 md:p-8 mb-6 border border-gray-100">
                        <h4 class="text-xl font-bold text-green-700 mb-6">1. Thông tin giao hàng</h4>

                        <div class="mb-4">
                            <label for="customer_name" class="block text-sm font-semibold text-gray-700 mb-2">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" id="customer_name" name="customer_name" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500 transition duration-150"
                                   value="{{ old('customer_name', $user->name ?? '') }}">
                        </div>

                        <div class="mb-4">
                            <label for="customer_phone" class="block text-sm font-semibold text-gray-700 mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="text" id="customer_phone" name="customer_phone" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500 transition duration-150"
                                   value="{{ old('customer_phone', $user->phone ?? '') }}">
                        </div>

                        <div class="mb-4">
                            <label for="customer_address" class="block text-sm font-semibold text-gray-700 mb-2">Địa chỉ <span class="text-red-500">*</span></label>
                            <input type="text" id="customer_address" name="customer_address" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500 transition duration-150"
                                   value="{{ old('customer_address', $user->address ?? '') }}">
                        </div>

                        <div class="mb-0">
                            <label for="note" class="block text-sm font-semibold text-gray-700 mb-2">Ghi chú (Tùy chọn)</label>
                            <textarea id="note" name="note" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500 transition duration-150"
                                      placeholder="Yêu cầu về thời gian giao hàng, quà tặng...">{{ old('note') }}</textarea>
                        </div>
                    </div>

                    {{-- PHƯƠNG THỨC THANH TOÁN --}}
                    <div class="bg-white shadow-xl rounded-xl p-6 md:p-8 border border-gray-100">
                        <h4 class="text-xl font-bold text-green-700 mb-6">2. Phương thức thanh toán</h4>

                        <div class="space-y-4">
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150">
                                <input type="radio" name="payment_method" value="cod" checked required
                                       class="form-radio h-5 w-5 text-green-600">
                                <span class="ml-3 text-base font-semibold text-gray-800">COD – Thanh toán khi nhận hàng</span>
                            </label>

                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150">
                                <input type="radio" name="payment_method" value="zalopay" required
                                       class="form-radio h-5 w-5 text-green-600">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/71/ZaloPay_logo.svg/1024px-ZaloPay_logo.svg.png"
                                     class="h-6 ml-3 mr-2" alt="ZaloPay Logo">
                                <span class="text-base font-semibold text-gray-800">Thanh toán qua ZaloPay</span>
                            </label>
                        </div>
                    </div>

                </div>

                {{-- CỘT 2: TÓM TẮT ĐƠN HÀNG --}}
                <div class="w-full lg:w-5/12 order-1 lg:order-2">
                    <div class="bg-white shadow-xl rounded-xl p-6 md:p-8 border border-gray-100 sticky top-4">
                        <h4 class="flex justify-between items-center text-xl font-bold text-green-700 mb-6 pb-2 border-b border-gray-200">
                            <span>Tóm tắt đơn hàng</span>
                            <span class="bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded-full">{{ count($cartItems) }} sản phẩm</span>
                        </h4>

                        <ul class="divide-y divide-gray-200 mb-6">
                            @foreach($cartItems as $item)
                                <li class="flex justify-between items-center py-3">
                                    <div>
                                        <p class="text-base font-medium text-gray-900">{{ $item->variant->product->name }}</p>
                                        <small class="text-sm text-gray-500">SL: {{ $item->quantity }} x {{ number_format($item->price) }}₫</small>
                                    </div>
                                    <span class="text-base font-semibold text-gray-700">{{ number_format($item->quantity * $item->price) }}₫</span>
                                </li>
                            @endforeach

                            <li class="flex justify-between items-center py-3 text-gray-700">
                                <span class="font-normal">Tạm tính</span>
                                <strong class="text-gray-900">{{ number_format($total) }}₫</strong>
                            </li>
                            <li class="flex justify-between items-center py-3 text-gray-700">
                                <span class="font-normal">Phí vận chuyển</span>
                                <strong class="text-green-600">Miễn phí</strong>
                            </li>
                            <li class="flex justify-between items-center pt-4 border-t-2 border-green-200 mt-2">
                                <span class="font-bold text-xl text-green-700">TỔNG CỘNG:</span>
                                <strong class="text-xl font-extrabold text-green-700">{{ number_format($total) }}₫</strong>
                            </li>
                        </ul>

                        <div id="qr-container" class="border border-yellow-400 bg-yellow-50 text-center p-4 rounded-lg hidden mt-6">
                            <div class="text-lg font-bold text-yellow-800 mb-3">Quét mã QR để thanh toán</div>
                            <div class="flex flex-col items-center">
                                <p class="text-sm text-gray-600 mb-2">Tổng tiền: <span id="qr-total" class="font-bold text-red-600">{{ number_format($total) }}₫</span></p>
                                <img id="qr-image" class="w-48 h-48 border border-gray-300 p-2 rounded-lg"
                                     src="https://placehold.co/220x220/E86850/white?text=QR+ZaloPay" alt="ZaloPay QR Code">
                                <button type="button" id="check-payment" class="bg-green-100 hover:bg-green-200 text-green-800 font-bold py-2 px-4 rounded-lg transition mt-4 w-full md:w-auto">
                                    Kiểm tra thanh toán
                                </button>
                                <div id="payment-status" class="mt-3 text-sm"></div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="mt-8">
                <button type="submit" id="submit-button" class="w-full bg-green-600 hover:bg-green-700 text-white font-extrabold text-lg py-4 rounded-xl shadow-lg transition duration-200">
                    HOÀN TẤT ĐẶT HÀNG
                </button>
            </div>
        </form>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const qrContainer = document.getElementById('qr-container');
    const submitButton = document.getElementById('submit-button');

    function updatePayment() {
        const selected = document.querySelector('input[name="payment_method"]:checked').value;
        if(selected === 'zalopay'){
            qrContainer.classList.remove('hidden');
            submitButton.textContent = 'THANH TOÁN QUA ZALOPAY';
            submitButton.classList.replace('bg-green-600','bg-blue-600');
            submitButton.classList.replace('hover:bg-green-700','hover:bg-blue-700');
        } else {
            qrContainer.classList.add('hidden');
            submitButton.textContent = 'HOÀN TẤT ĐẶT HÀNG';
            submitButton.classList.replace('bg-blue-600','bg-green-600');
            submitButton.classList.replace('hover:bg-blue-700','hover:bg-green-700');
        }
    }

    paymentRadios.forEach(r => r.addEventListener('change', updatePayment));
    updatePayment();

    document.getElementById('check-payment')?.addEventListener('click', function(){
        const statusDiv = document.getElementById('payment-status');
        statusDiv.innerHTML = '<div class="flex items-center justify-center text-green-600"><svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Đang kiểm tra...</div>';
        setTimeout(()=>{statusDiv.innerHTML='<span class="text-green-600 font-bold">✅ Thanh toán thành công!</span>';},3000);
    });
});
</script>
@endpush
@endsection
