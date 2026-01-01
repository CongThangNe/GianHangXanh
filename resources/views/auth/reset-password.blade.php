@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<div class="relative min-h-screen flex items-center justify-center bg-gray-100">

    <!-- Background + Overlay -->
    <div class="absolute inset-0 bg-cover bg-center"
         style="background-image: url('https://img.freepik.com/premium-photo/ecommerce-concept-delivery-service-from-front-store-transportation-delivery-by-vans-truck-motorbike-scooter-product-packages-gift-boxes-tree-low-polygon-green-tone-3d-rendering_1226542-3726.jpg?w=1380');">
        <div class="absolute inset-0 bg-green-900 bg-opacity-50"></div>
    </div>

    <!-- Form Container -->
    <div class="relative w-full max-w-md bg-white bg-opacity-90 shadow-lg rounded-lg p-6 z-10 mx-4">
        <h2 class="text-2xl font-bold text-center text-green-600 mb-6">Đặt lại mật khẩu</h2>

        @if ($errors->any())
            <div class="mb-3 text-red-600 font-semibold text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $email) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Email"
                    required>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mật khẩu mới</label>
                <input
                    type="password"
                    name="password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Nhập mật khẩu mới"
                    required>
            </div>

            <!-- Password Confirmation -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nhập lại mật khẩu</label>
                <input
                    type="password"
                    name="password_confirmation"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Nhập lại mật khẩu mới"
                    required>
            </div>

            <button
                type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition mt-2">
                Xác nhận đặt lại mật khẩu
            </button>

            <p class="text-center text-sm mt-4">
                <a href="{{ route('login') }}" class="text-green-600 font-semibold hover:underline">Quay lại đăng nhập</a>
            </p>
        </form>
    </div>
</div>
@endsection
