@extends('layouts.app')

@section('title', 'Đăng ký tài khoản')

@section('content')
<div class="relative min-h-screen flex flex-col">

    <!-- Background + Overlay -->
    <div class="absolute inset-0 bg-cover bg-center"
         style="background-image: url('https://img.freepik.com/premium-photo/ecommerce-concept-delivery-service-from-front-store-transportation-delivery-by-vans-truck-motorbike-scooter-product-packages-gift-boxes-tree-low-polygon-green-tone-3d-rendering_1226542-3726.jpg?w=1380');">
        <div class="absolute inset-0 bg-green-900 bg-opacity-50"></div>
    </div>

    <!-- Main Form Section -->
    <main class="relative z-10 flex-grow flex items-center justify-center px-4">
        <div class="w-full max-w-md bg-white bg-opacity-90 shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-center text-green-600 mb-6">Tạo tài khoản mới</h2>

            <!-- Error -->
            @if ($errors->any())
                <div class="mb-3 text-red-600 font-semibold text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Họ và tên</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Nhập họ tên">
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Nhập email">
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Mật khẩu</label>
                    <input
                        type="password"
                        name="password"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Nhập mật khẩu">
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Xác nhận mật khẩu</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Nhập lại mật khẩu">
                </div>

                <!-- Button -->
                <button
                    type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition">
                    Đăng ký
                </button>

                <p class="text-center text-sm mt-4">
                    Đã có tài khoản?
                    <a href="{{ route('login') }}" class="text-green-600 font-semibold hover:underline">
                        Đăng nhập ngay
                    </a>
                </p>
            </form>
        </div>
    </main>
</div>
@endsection
