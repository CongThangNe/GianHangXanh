@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6">

        <h2 class="text-2xl font-bold text-center text-green-600 mb-6">
            Đăng nhập
        </h2>

        <!-- Form -->
        <form method="POST" action="#">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input
                    type="email"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Nhập email của bạn">
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mật khẩu</label>
                <input
                    type="password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Nhập mật khẩu">
            </div>

            <!-- Button -->
            <button
                type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition mt-2">
                Đăng nhập
            </button>

            <p class="text-center text-sm mt-4">
                Chưa có tài khoản?
                <a href="{{ route('register') }}" class="text-green-600 font-semibold hover:underline">
                    Đăng ký ngay
                </a>
            </p>
        </form>
    </div>
</div>
@endsection
