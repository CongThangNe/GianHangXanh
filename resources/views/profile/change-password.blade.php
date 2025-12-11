@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">

    <!-- <h1 class="text-2xl font-bold mb-6">Đổi mật khẩu</h1> -->

    {{-- Thông báo thành công --}}
    @if (session('success_password'))
        <div class="mb-4 rounded border border-green-400 bg-green-50 px-4 py-3 text-green-800">
            {{ session('success_password') }}
        </div>
    @endif

    {{-- Thông báo lỗi --}}
    @if ($errors->any())
        <div class="mb-4 rounded border border-red-400 bg-red-50 px-4 py-3 text-red-800">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow rounded-xl p-6">

        <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block font-medium mb-1">Mật khẩu hiện tại</label>
                <input type="password" 
                       name="current_password" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" 
                       required>
                @error('current_password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-medium mb-1">Mật khẩu mới</label>
                <input type="password" 
                       name="password" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" 
                       required>
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-medium mb-1">Xác nhận mật khẩu mới</label>
                <input type="password" 
                       name="password_confirmation" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" 
                       required>
            </div>

            <div class="text-center">
                <button class="px-5 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700">
                    Đổi mật khẩu
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
