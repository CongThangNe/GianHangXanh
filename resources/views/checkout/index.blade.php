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

                        <div class="mb-4">
                            <label for="customer_email" class="block text-sm font-semibold text-gray-700 mb-2">Email (nếu có)</label>
                            <input type="email" id="customer_email" name="customer_email"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500 transition duration-150"
                                   value="{{ old('customer_email', $user->email ?? '') }}">
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Ghi chú đơn hàng</label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500 transition duration-150">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    {{-- PHƯƠNG THỨC THANH TOÁN --}}
                    <div class="bg-white shadow-xl rounded-xl p-6 md:p-8 border border-gray-100">
                        <h4 class="text-xl font-bold text-green-700 mb-6">2. Phương thức thanh toán</h4>

                        <div class="flex flex-col gap-4">
                            <!-- Thanh toán khi nhận hàng -->
                            <div class="border border-gray-300 rounded-lg p-4 hover:border-green-500 transition duration-150">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="payment_method" value="cod" checked class="form-radio text-green-500 focus:ring-green-500">
                                    <span class="ml-3 font-semibold text-gray-800">Thanh toán khi nhận hàng (COD)</span>
                                </label>
                                <p class="mt-2 text-sm text-gray-600 ml-7">Khách hàng thanh toán bằng tiền mặt khi nhận hàng. Vui lòng kiểm tra kỹ sản phẩm trước khi thanh toán.</p>
                            </div>

                            <!-- ZaloPay -->
                            <div class="border border-gray-300 rounded-lg p-4 hover:border-green-500 transition duration-150">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="payment_method" value="zalopay" class="form-radio text-green-500 focus:ring-green-500">
                                    <span class="ml-3 font-semibold text-gray-800">Thanh toán qua ZaloPay</span>
                                </label>
                                <p class="mt-2 text-sm text-gray-600 ml-7">Quét mã QR hoặc chuyển khoản qua ứng dụng ZaloPay. Thanh toán nhanh chóng và an toàn.</p>
                                <div id="qr-container" class="hidden mt-4 bg-gray-50 p-4 rounded-lg text-center">
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

                {{-- CỘT 2: CHI TIẾT ĐƠN HÀNG --}}
                <div class="w-full lg:w-5/12 order-1 lg:order-2">
                    <div class="bg-white shadow-xl rounded-xl p-6 md:p-8 sticky top-24 border border-gray-100">
                        <h4 class="text-xl font-bold text-green-700 mb-6">Chi tiết đơn hàng</h4>

                        <div class="space-y-4 mb-6">
                            @foreach($cartItems as $item)
                                <div class="flex items-start gap-4 pb-4 border-b last:border-b-0">
                                    <img src="{{ asset('storage/' . $item->variant->product->image ?? 'placeholder.jpg') }}" 
                                         alt="{{ $item->variant->product->name ?? 'Sản phẩm' }}" 
                                         class="w-16 h-16 object-cover rounded-lg border border-gray-200 shadow-sm">

                                    <div class="flex-1">
                                        <h5 class="font-semibold text-gray-800 line-clamp-2">{{ $item->variant->product->name ?? 'Sản phẩm không tồn tại' }}</h5>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Phân loại: {{ $item->variant->attribute_value ?? 'Mặc định' }}
                                        </p>
                                        <div class="flex justify-between mt-2">
                                            <span class="text-sm font-medium text-gray-600">SL: {{ $item->quantity }}</span>
                                            <span class="text-sm font-bold text-green-600">{{ number_format($item->price * $item->quantity) }}₫</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-3 border-t pt-4">
                            <div class="flex justify-between text-sm font-medium">
                                <span class="text-gray-700">Tạm tính</span>
                                <span class="text-gray-900">{{ number_format($subtotal) }}₫</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium">
                                <span class="text-gray-700">Vận chuyển</span>
                                <span class="text-green-600">Miễn phí</span>
                            </div>
                            @if($discountAmount > 0)
                            <div class="flex justify-between text-sm font-medium">
                                <span class="text-gray-700">Giảm giá</span>
                                <span class="text-red-600">-{{ number_format($discountAmount) }}₫</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-lg font-bold pt-2 border-t">
                                <span class="text-gray-800">Tổng thanh toán</span>
                                <span class="text-green-600">{{ number_format($total) }}₫</span>
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
