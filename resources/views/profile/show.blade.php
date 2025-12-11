@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">

    <div class="text-center">
        <h1 class="text-2xl font-bold mb-6">Hồ sơ cá nhân</h1>
    </div>


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

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

        {{-- SIDEBAR BÊN TRÁI --}}
        <div class="md:col-span-1">
            <div class="bg-white shadow rounded-xl p-4">

                {{-- Avatar --}}
                <div class="w-28 h-28 rounded-full overflow-hidden mx-auto mb-4 border">
                    @if ($user->avatar_path)
                        <img src="{{ asset('storage/' . $user->avatar_path) }}"
                             class="w-full h-full object-cover" alt="Avatar">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                            <span class="material-symbols-outlined text-5xl">account_circle</span>
                        </div>
                    @endif
                </div>

                <p class="text-center font-semibold">{{ $user->name }}</p>
                <p class="text-center text-sm text-gray-500 mb-6">{{ $user->email }}</p>

                {{-- MENU SIDEBAR --}}
                <div class="space-y-2 mt-4">
                    <!-- <a href="{{ route('profile.show') }}"
                       class="block px-4 py-2 rounded-md font-medium 
                              {{ request()->routeIs('profile.show') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        Hồ sơ cá nhân
                    </a> -->

                    <a href="{{ route('user.orders.index') }}"
                    class="block px-4 py-2 rounded-md font-medium text-center
                            {{ request()->routeIs('user.orders.index') 
                                ? 'bg-green-600 text-white' 
                                : 'text-gray-700 hover:bg-gray-100' }}">
                        Đơn hàng của tôi
                    </a>

                    <a href="{{ route('profile.password') }}"
                    class="block px-4 py-2 rounded-md font-medium text-center
                            {{ request()->routeIs('profile.password') ? 'bg-green-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        Đổi mật khẩu
                    </a>


                </div>

            </div>
        </div>

        {{-- CỘT PHẢI: FORM --}}
        <div class="md:col-span-3 space-y-8">

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
                            class="inline-flex items-center px-4 py-2 rounded-md bg-primary text-white text-sm font-medium hover:bg-green-700">
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>

            <!-- {{-- Form đổi mật khẩu --}}
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
                            class="inline-flex items-center px-4 py-2 rounded-md bg-primary text-white text-sm font-medium hover:bg-green-700">
                            Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </div> -->

        </div>
    </div>
</div>
@endsection
