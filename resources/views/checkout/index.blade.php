@extends('layouts.app')
@section('title', 'Thanh Toán')

@section('content')
    <div class="container mx-auto px-4 py-8 md:py-12 max-w-7xl">

        {{-- TIÊU ĐỀ --}}
        <!-- <h2 class="text-3xl md:text-4xl font-extrabold text-center text-green-700 mb-8 md:mb-12">
                                                                                                        Thanh Toán Đơn Hàng
                                                                                                    </h2> -->

        {{-- THÔNG BÁO LỖI --}}
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- KIỂM TRA GIỎ HÀNG --}}
        @if (empty($cartItems) || $cartItems->isEmpty())
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
                                <label for="customer_name" class="block text-sm font-semibold text-gray-700 mb-2">Họ và tên
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="customer_name" name="customer_name"
                                    class="w-full border @error('customer_name') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500 transition duration-150"
                                    value="{{ old('customer_name', $user->name ?? '') }}">
                                @error('customer_name')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="customer_phone" class="block text-sm font-semibold text-gray-700 mb-2">Số điện
                                    thoại <span class="text-red-500">*</span></label>
                                <input type="text" id="customer_phone" name="customer_phone"
                                    class="w-full border @error('customer_phone') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500 transition duration-150"
                                    value="{{ old('customer_phone', $user->phone ?? '') }}">
                                @error('customer_phone')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="customer_address" class="block text-sm font-semibold text-gray-700 mb-2">Địa chỉ
                                    <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <select id="province" name="province"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500">
                                            <option value="">Chọn Tỉnh / Thành phố</option>
                                        </select>
                                        @error('province')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <select id="ward" name="ward"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500"
                                            disabled>
                                            <option value="">Chọn Phường / Xã</option>
                                        </select>
                                        @error('ward')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <input type="text" id="address_detail" name="address_detail"
                                    placeholder="Số nhà, tên đường..."
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-2">

                                {{-- Ô nhập số nhà
                                <input type="text" id="address_detail" name="address_detail"
                                    value="{{ old('address_detail') }}" placeholder="Số nhà, tên đường..."
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 mb-2"> --}}
                                @error('address_detail')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror

                                {{-- Input ẨN duy nhất để lưu địa chỉ tổng hợp --}}
                                <input type="hidden" id="full_customer_address" name="customer_address"
                                    value="{{ old('customer_address') }}">

                                @error('customer_address')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block font-medium">
                                    Email <span class="text-gray-500"></span>
                                </label>
                                <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                    class="w-full border rounded px-3 py-2" placeholder="example@gmail.com">
                            </div>


                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Ghi chú đơn
                                    hàng</label>
                                <textarea id="notes" name="notes" rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500 transition duration-150">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        {{-- PHƯƠNG THỨC THANH TOÁN --}}
                        <div class="bg-white shadow-xl rounded-xl p-6 md:p-8 border border-gray-100">
                            <h4 class="text-xl font-bold text-green-700 mb-6">2. Phương thức thanh toán</h4>

                            <div class="flex flex-col gap-4">
                                <!-- Thanh toán khi nhận hàng -->
                                <div
                                    class="border border-gray-300 rounded-lg p-4 hover:border-green-500 transition duration-150">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="payment_method" value="cod" checked
                                            class="form-radio text-green-500 focus:ring-green-500">
                                        <span class="ml-3 font-semibold text-gray-800">Thanh toán khi nhận hàng (COD)</span>
                                    </label>
                                    <p class="mt-2 text-sm text-gray-600 ml-7">Khách hàng thanh toán bằng tiền mặt khi nhận
                                        hàng. Vui lòng kiểm tra kỹ sản phẩm trước khi thanh toán.</p>
                                </div>

                                <!-- ZaloPay -->
                                <div
                                    class="border border-gray-300 rounded-lg p-4 hover:border-green-500 transition duration-150">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="payment_method" value="zalopay"
                                            class="form-radio text-green-500 focus:ring-green-500">
                                        <span class="ml-3 font-semibold text-gray-800">Thanh toán qua ZaloPay</span>
                                    </label>
                                    <p class="mt-2 text-sm text-gray-600 ml-7">Quét mã QR hoặc chuyển khoản qua ứng dụng
                                        ZaloPay. Thanh toán nhanh chóng và an toàn.</p>
                                    <div id="qr-container" class="hidden mt-4 bg-gray-50 p-4 rounded-lg text-center">
                                        <p class="text-sm text-gray-600 mb-2">Tổng tiền: <span id="qr-total"
                                                class="font-bold text-red-600">{{ number_format($total) }}₫</span></p>
                                        <img id="qr-image" class="w-48 h-48 border border-gray-300 p-2 rounded-lg"
                                            src="https://placehold.co/220x220/E86850/white?text=QR+ZaloPay"
                                            alt="ZaloPay QR Code">
                                        <button type="button" id="check-payment"
                                            class="bg-green-100 hover:bg-green-200 text-green-800 font-bold py-2 px-4 rounded-lg transition mt-4 w-full md:w-auto">
                                            Kiểm tra thanh toán
                                        </button>
                                        <div id="payment-status" class="mt-3 text-sm"></div>
                                    </div>
                                </div>

                                {{-- VNPAY  --}}
                                <div
                                    class="border border-gray-300 rounded-lg p-4 hover:border-green-500 transition duration-150">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="payment_method" value="vnpay"
                                            class="form-radio text-green-500 focus:ring-green-500">
                                        <span class="ml-3 font-semibold text-gray-800">Thanh toán qua VNPAY (Mã QR)</span>
                                        <img src="https://vnpay.vn/assets/images/logo-icon/logo-primary.svg"
                                            alt="VNPAY" class="h-6 ml-auto">
                                    </label>
                                    <p class="mt-2 text-sm text-gray-600 ml-7">
                                        Hệ thống sẽ chuyển hướng sang cổng VNPAY để tạo mã QR an toàn. Bạn có thể quét bằng
                                        ứng dụng ngân hàng hoặc ví VNPAY.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- CỘT 2: CHI TIẾT ĐƠN HÀNG --}}
                    <div class="w-full lg:w-5/12 order-1 lg:order-2">
                        <div class="bg-white shadow-xl rounded-xl p-6 md:p-8 sticky top-24 border border-gray-100">
                            <h4 class="text-xl font-bold text-green-700 mb-6">Chi tiết đơn hàng</h4>

                            <div class="space-y-4 mb-6">
                                @foreach ($cartItems as $item)
                                    <div class="flex items-start gap-4 pb-4 border-b last:border-b-0">
                                        <img src="{{ asset('storage/' . $item->variant->product->image ?? 'placeholder.jpg') }}"
                                            alt="{{ $item->variant->product->name ?? 'Sản phẩm' }}"
                                            class="w-16 h-16 object-cover rounded-lg border border-gray-200 shadow-sm">

                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-800 line-clamp-2">
                                                {{ $item->variant->product->name ?? 'Sản phẩm không tồn tại' }}</h5>
                                            <p class="text-sm text-gray-500 mt-1">
                                                Phân loại: {{ $item->variant?->variant_label ?? 'Mặc định' }}
                                            </p>
                                            <div class="flex justify-between mt-2">
                                                <span class="text-sm font-medium text-gray-600">SL:
                                                    {{ $item->quantity }}</span>
                                                <span
                                                    class="text-sm font-bold text-green-600">{{ number_format($item->price * $item->quantity) }}₫</span>
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
                                <!-- Giữ nguyên cấu trúc cũ, chỉ thay đổi hiển thị giảm giá -->
                                @if ($discountInfo)
                                    <div class="flex justify-between text-base font-medium text-green-600">
                                        <span>Giảm giá ({{ $discountInfo['code'] }}):</span>
                                        <span>-{{ number_format($discountInfo['amount'], 0, ',', '.') }}đ</span>
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
                    <button type="submit" id="submit-button"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-extrabold text-lg py-4 rounded-xl shadow-lg transition duration-200">
                        HOÀN TẤT ĐẶT HÀNG
                    </button>
                </div>
            </form>
        @endif
    </div>

    @push('scripts')
        <script>
            const API_BASE = "{{ config('services.admin_location_api.base_url') }}";
            document.addEventListener('DOMContentLoaded', function() {
                const provinceSelect = document.getElementById('province');
                const wardSelect = document.getElementById('ward');
                const addressDetail = document.getElementById('address_detail');
                const fullAddressInput = document.getElementById('full_customer_address');

                const OLD_PROVINCE = "{{ old('province') }}";
                const OLD_WARD = "{{ old('ward') }}";

                function updateFullAddress() {
                    const provinceText = provinceSelect.options[provinceSelect.selectedIndex]?.text;
                    const wardText = wardSelect.options[wardSelect.selectedIndex]?.text;
                    const detail = addressDetail.value.trim();

                    if (provinceText && wardText && detail) {
                        fullAddressInput.value = `${detail}, ${wardText}, ${provinceText}`;
                    }
                }
                fetch(`${API_BASE}/new-provinces?limit=100`)
                    .then(res => res.json())
                    .then(res => {
                        if (!res.success) return;

                        provinceSelect.innerHTML = '<option value="">Chọn Tỉnh / Thành phố</option>';
                        res.data.forEach(p => {
                            provinceSelect.innerHTML += `
                    <option value="${p.code}" ${p.code === OLD_PROVINCE ? 'selected' : ''}>
                        ${p.name}
                    </option>`;
                        });

                        if (OLD_PROVINCE) provinceSelect.dispatchEvent(new Event('change'));
                    });
                provinceSelect.addEventListener('change', function() {
                    wardSelect.innerHTML = '<option value="">Chọn Phường / Xã</option>';
                    wardSelect.disabled = true;

                    if (!this.value) return;

                    fetch(`${API_BASE}/new-provinces/${this.value}/wards?limit=100`)
                        .then(res => res.json())
                        .then(res => {
                            if (!res.success) return;

                            res.data.forEach(w => {
                                wardSelect.innerHTML += `
                        <option value="${w.code}" ${w.code === OLD_WARD ? 'selected' : ''}>
                            ${w.name}
                        </option>`;
                            });

                            wardSelect.disabled = false;
                        });
                });

                provinceSelect.addEventListener('change', updateFullAddress);

                addressDetail.addEventListener('input', updateFullAddress);
                const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
                // const qrContainer = document.getElementById('qr-container');
                // const submitButton = document.getElementById('submit-button');

                function updatePayment() {
                    // Lấy giá trị đang chọn
                    const selected = document.querySelector('input[name="payment_method"]:checked').value;

                    // Reset lại các class màu sắc mặc định cho nút submit
                    submitButton.className =
                        'w-full text-white font-extrabold text-lg py-4 rounded-xl shadow-lg transition duration-200';

                    if (selected === 'zalopay') {
                        // Logic cũ của ZaloPay
                        qrContainer.classList.remove('hidden');
                        submitButton.textContent = 'THANH TOÁN QUA ZALOPAY';
                        submitButton.classList.add('bg-blue-600', 'hover:bg-blue-700');

                    } else if (selected === 'vnpay') {
                        // Logic MỚI cho VNPAY
                        qrContainer.classList.add('hidden'); // Ẩn QR mockup vì VNPAY dùng QR thật ở trang đích
                        submitButton.textContent = 'THANH TOÁN QUA VNPAY';

                        // Đổi màu nút sang màu đặc trưng VNPAY (đỏ/cam) hoặc giữ xanh
                        submitButton.classList.add('bg-red-600', 'hover:bg-red-700');

                    } else {
                        // Logic cho COD
                        qrContainer.classList.add('hidden');
                        submitButton.textContent = 'HOÀN TẤT ĐẶT HÀNG';
                        submitButton.classList.add('bg-green-600', 'hover:bg-green-700');
                    }
                }

                paymentRadios.forEach(r => r.addEventListener('change', updatePayment));
                updatePayment();

                document.getElementById('check-payment')?.addEventListener('click', function() {
                    const statusDiv = document.getElementById('payment-status');
                    statusDiv.innerHTML =
                        '<div class="flex items-center justify-center text-green-600"><svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Đang kiểm tra...</div>';
                    setTimeout(() => {
                        statusDiv.innerHTML =
                            '<span class="text-green-600 font-bold">✅ Thanh toán thành công!</span>';
                    }, 3000);
                });


                document.getElementById('checkout-form').addEventListener('submit', function() {
                    updateFullAddress();
                });
                document.getElementById('checkout-form').addEventListener('submit', function(e) {
                    if (!provinceSelect.value) {
                        alert('Vui lòng chọn Tỉnh / Thành phố');
                        e.preventDefault();
                        return;
                    }
                    if (!wardSelect.value) {
                        alert('Vui lòng chọn Phường / Xã');
                        e.preventDefault();
                        return;
                    }
                    if (!addressDetail.value.trim()) {
                        alert('Vui lòng nhập số nhà, tên đường');
                        e.preventDefault();
                        return;
                    }
                });

            });
        </script>
    @endpush
@endsection
