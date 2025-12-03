@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <h1 class="text-2xl font-bold mb-6">Hồ sơ cá nhân</h1>

    {{-- Thông báo chung --}}
    @if (session('success'))
        <div class="mb-4 rounded border border-green-400 bg-green-50 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('success_password'))
        <div class="mb-4 rounded border border-green-400 bg-green-50 px-4 py-3 text-green-800">
            {{ session('success_password') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded border border-red-400 bg-red-50 px-4 py-3 text-red-800">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Cột trái: Avatar --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
                <div class="w-32 h-32 rounded-full overflow-hidden mb-4 border">
                    @if ($user->avatar_path)
                        <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                            <span class="material-symbols-outlined text-5xl">account_circle</span>
                        </div>
                    @endif
                </div>
                <p class="font-semibold">{{ $user->name }}</p>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>
        </div>

        {{-- Cột phải: Form cập nhật thông tin và đổi mật khẩu --}}
        <div class="md:col-span-2 space-y-8">

            {{-- Form cập nhật thông tin hồ sơ --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Thông tin hồ sơ</h2>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium mb-1" for="name">Họ và tên</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" for="phone">Số điện thoại</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" for="address">Địa chỉ</label>
                        <input id="address" name="address" type="text" value="{{ old('address', $user->address) }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" for="avatar">Ảnh đại diện</label>
                        <input id="avatar" name="avatar" type="file"
                            class="block w-full text-sm text-gray-700">
                        <p class="mt-1 text-xs text-gray-500">Hỗ trợ: JPG, PNG, GIF, WEBP. Tối đa 2MB.</p>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 rounded-md bg-primary text-white text-sm font-medium hover:bg-green-700 focus:outline-none">
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>

            {{-- Form đổi mật khẩu --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Đổi mật khẩu</h2>

                <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium mb-1" for="current_password">Mật khẩu hiện tại</label>
                        <input id="current_password" name="current_password" type="password"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" for="password">Mật khẩu mới</label>
                        <input id="password" name="password" type="password"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" for="password_confirmation">Nhập lại mật khẩu mới</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 rounded-md bg-primary text-white text-sm font-medium hover:bg-green-700 focus:outline-none">
                            Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
