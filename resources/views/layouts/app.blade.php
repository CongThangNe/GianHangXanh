<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gian Hàng Xanh - Sản phẩm bền vững cho hành tinh tốt đẹp hơn')</title>

    <!-- Tailwind CSS -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">

    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

    <!-- Tailwind Config -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#13ec13",
                        "background-light": "#f6f8f6",
                        "background-dark": "#102210",
                        "text-light": "#111811",
                        "text-dark": "#f0f4f0",
                        "subtle-light": "#618961",
                        "subtle-dark": "#a2c3a2",
                        "surface-light": "#ffffff",
                        "surface-dark": "#1a2d1a",
                        "border-light": "#e0e6e0",
                        "border-dark": "#2a3d2a",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>

    <style>
        body {
            background-color: #f9f9f9;
        }

        footer {
            background-color: #2e7d32;
            color: white;
            padding: 20px 0;
            margin-top: 40px;
        }

        footer a {
            color: #c8e6c9;
            text-decoration: none;
            font-size: 1.3rem;
            transition: color .3s;
        }

        footer a:hover {
            color: #fff176;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-text-light dark:text-text-dark">
    <div class="relative flex min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">

            <!-- TopNavBar -->
            <header
                class="sticky top-0 z-50 flex justify-center bg-green-700 backdrop-blur-sm border-b border-solid border-border-light dark:border-border-dark text-white">
                <div class="flex w-full max-w-7xl items-center justify-between whitespace-nowrap px-4 py-3">

                    <!-- Left: Logo & Navigation -->
                    <div class="flex items-center gap-8">

                        <!-- Logo -->
                        <div class="flex items-center gap-2 text-white">
                            <img src="{{ asset('storage/uploads/logos/logo.png') }}" alt="Gian Hàng Xanh"
                                class="h-10 w-10 rounded-full object-cover">

                            <a class="text-xl font-bold" href="{{ url('/') }}">
                                <h2>Gian Hàng Xanh</h2>
                            </a>
                        </div>

                        <!-- Desktop Navigation -->
                        <nav class="hidden md:flex items-center gap-6">
                            <a class="text-sm font-medium hover:text-primary" href="{{ url('/') }}">
                                Trang chủ
                            </a>

                            <a class="text-sm font-medium hover:text-primary" href="{{ route('cart.index') }}">
                                Giỏ hàng
                            </a>
                            <a class="text-sm font-medium hover:text-primary" href="{{ route('intro') }}">
                                Giới thiệu
                            </a>
                            <a class="text-sm font-medium hover:text-primary" href="{{ route('support.index') }}">
                                Liên hệ & Hỗ trợ
                            </a>
                            {{-- <a class="text-sm font-medium hover:text-primary" href="{{ route('news.index') }}">
                                Tin tức
                            </a> --}}

                        </nav>
                    </div>

                    <!-- Right: Search & Auth -->
                    <div class="flex flex-1 justify-end items-center gap-4">

                        <!-- Search Bar -->
                        <form action="{{ route('home') }}" method="GET"
                            class="hidden sm:flex flex-col min-w-40 !h-10 max-w-64">

                            <div class="flex items-stretch h-full border border-white rounded-lg overflow-hidden">

                                <button type="submit"
                                    class="text-white flex bg-green-100/50 items-center justify-center pl-3 pr-2 rounded-l-lg border border-r-0">
                                    <span class="material-symbols-outlined text-white"
                                        style="font-size:20px">Search</span>
                                </button>

                                <input type="search" name="keyword"
                                    class="form-input flex w-full text-sm text-white bg-green-100/50 px-3"
                                    placeholder="Tìm kiếm sản phẩm..." value="{{ request('keyword') }}">
                            </div>
                        </form>

                        <!-- Auth Section -->
                        @guest
                            <div class="hidden md:flex items-center gap-3">
                                <a class="text-sm font-medium hover:text-primary" href="{{ route('login') }}">Đăng nhập</a>
                                <a class="text-sm font-medium hover:text-primary" href="{{ route('register') }}">Đăng ký</a>
                            </div>
                        @else
                            <!-- User Dropdown -->
                            <div class="hidden md:block relative" x-data="{ open: false }">
                                <button @click="open = !open" @click.away="open = false"
                                    class="flex items-center gap-2 text-sm font-medium text-white hover:text-primary">
                                    <span class="material-symbols-outlined" style="font-size:20px">account_circle</span>
                                    <span>{{ Auth::user()->name }}</span>
                                    <span class="material-symbols-outlined" style="font-size:16px">expand_more</span>
                                </button>

                                <div x-show="open" x-transition
                                    class="absolute right-0 mt-2 w-48 bg-green-600 border rounded-lg shadow-lg py-1">

                                    <a href="{{ route('profile.show') }}"
                                        class="block px-4 py-2 text-sm hover:bg-green-500">
                                        Hồ sơ cá nhân
                                    </a>

                                    <a href="{{ route('user.orders.index') }}"
                                        class="block px-4 py-2 text-sm hover:bg-green-500">
                                        Đơn hàng
                                    </a>

                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-green-500">
                                            Đăng xuất
                                        </button>
                                    </form>

                                    @php
                                        $role = Auth::user()->role ?? null;
                                    @endphp

                                    @if ($role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="block px-4 py-2 text-sm hover:bg-green-500">
                                            Admin
                                        </a>
                                    @elseif ($role === 'staff')
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="block px-4 py-2 text-sm hover:bg-green-500">
                                            Quản lý
                                        </a>
                                    @endif

                                </div>
                            </div>
                        @endguest
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="max-w-7xl w-full mx-auto px-4 py-6">
                

                @yield('content')
            </div>

        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-green-700 text-white text-sm pt-12 pb-6">
        <div class="container mx-auto px-6">

            <!-- ==== CỘT LOGO + THÔNG TIN ==== -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mt-6 text-sm">

                <!-- Cột Logo -->
                <img src="{{ asset('storage/uploads/logos/logo.png') }}" alt="Gian Hàng Xanh"
                    class="h-40 w-auto object-contain">

                <!-- Cột 1 -->
                <div class="text-xs">
                    <h4 class="font-bold mb-2 text-lg">VỀ CHÚNG TÔI</h4>
                    <p>Gian Hàng Xanh – cung cấp sản phẩm xanh, thân thiện môi trường.</p>
                    <p class="mt-2">Địa chỉ: Hà Nội</p>
                    <p>Hotline: 035.2614.404</p>
                </div>

                <!-- Cột 2 -->
                <div class="text-xs">
                    <h4 class="font-bold mb-2 text-lg">CHÍNH SÁCH</h4>
                    <ul class="space-y-1">
                        <li>- Bảo hành</li>
                        <li>- Đổi trả</li>
                        <li>- Vận chuyển</li>
                        <li>- Thanh toán</li>
                    </ul>
                </div>

                <!-- Cột 3 -->
                <div class="text-xs">
                    <h4 class="font-bold mb-2 text-lg">HỖ TRỢ KHÁCH HÀNG</h4>
                    <ul class="space-y-1">
                        <li>- Hướng dẫn mua hàng</li>
                        <li>- Hướng dẫn thanh toán</li>
                        <li>- Câu hỏi thường gặp</li>
                        <li>- Liên hệ hỗ trợ</li>
                    </ul>
                </div>

                <!-- Cột 4 -->
                <div class="text-xs">
                    <h4 class="font-bold mb-2 text-lg">LÝ DO CHỌN CHÚNG TÔI</h4>
                    <ul class="space-y-1">
                        <li>- Chất lượng cao</li>
                        <li>- Ship toàn quốc</li>
                        <li>- Giá hợp lý</li>
                        <li>- Hỗ trợ tận tâm</li>
                    </ul>
                </div>
            </div>

            <!-- ==== DÒNG CUỐI ==== -->
            <div class="mt-10 flex flex-col md:flex-row justify-between items-center border-t border-green-300 pt-4">
                <p class="text-xs opacity-80">© 2025 Gian Hàng Xanh</p>
                <p class="text-center text-xs mt-4 md:mt-0">
                    Gian Hàng Xanh – Lan tỏa sản phẩm thân thiện môi trường.
                </p>

                <div class="flex space-x-4 text-xl mt-4 md:mt-0">
                    <a href="#" class="hover:text-green-200"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="hover:text-green-200"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="hover:text-green-200"><i class="bi bi-envelope"></i></a>
                </div>

            </div>

        </div>
    </footer>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
</body>

</html>
