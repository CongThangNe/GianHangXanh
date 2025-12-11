<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gian Hàng Xanh - Sản phẩm bền vững cho hành tinh tốt đẹp hơn')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

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
                        "border-dark": "#2a3d2a"
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
                class="sticky top-0 z-50 flex justify-center bg-green-500/80 backdrop-blur-sm border-b border-solid border-border-light dark:border-border-dark text-white">
                <div class="flex w-full max-w-7xl items-center justify-between whitespace-nowrap px-4 py-3">

                    <!-- Left: Logo & Navigation -->
                    <div class="flex items-center gap-8">

                        <!-- Logo -->
                        <div class="flex items-center gap-2 text-white">
                                <img src="{{ asset('storage/uploads/logos/logo.png') }}" alt="Gian Hàng Xanh"
                                    class="h-16 rounded-full object-cover">


                           

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

                            {{-- ❌ ĐÃ XOÁ ADMIN KHỎI MENU NGANG --}}
                        </nav>
                    </div>

                    <!-- Right: Search & Auth -->
                    <div class="flex flex-1 justify-end items-center gap-4">

                        <!-- Search Bar -->
                        <form action="{{ route('home') }}" method="GET"
                            class="hidden sm:flex flex-col min-w-40 !h-10 max-w-64">
                            <div class="flex items-stretch rounded-lg h-full">
                                <button type="submit"
                                    class="text-white flex bg-green-100/50 items-center justify-center pl-3 pr-2 rounded-l-lg border border-r-0">
                                    <span class="material-symbols-outlined text-white"
                                        style="font-size:20px">Search</span>
                                </button>
                                <input type="search" name="keyword"
                                    class="form-input flex w-full text-sm text-white bg-green-100/50 px-3 rounded-r-lg"
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
                                    class="absolute right-0 mt-2 w-48 bg-green-200 border rounded-lg shadow-lg py-1">

                                    <a href="{{ route('profile.show') }}"
                                        class="block px-4 py-2 text-sm hover:bg-green-300">
                                        Hồ sơ cá nhân
                                    </a>

                                    <a href="{{ route('user.orders.index') }}"
                                        class="block px-4 py-2 text-sm hover:bg-green-300">
                                        Đơn hàng
                                    </a>

                                    <hr class="my-1">

                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-green-300">
                                            Đăng xuất
                                        </button>
                                    </form>

                                    <!-- ⭐ THÊM ADMIN VÀO NGAY SAU ĐĂNG XUẤT -->
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="block px-4 py-2 text-sm hover:bg-green-300 border-t">
                                        Admin
                                    </a>

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
    <footer class="bg-green-500 text-white">
        <div class="container py-2">
            <div class="row align-items-center">
                <div class="col-12 col-md-4 text-center text-md-start mb-2">
                    <img src="https://via.placeholder.com/100x40?text=LOGO" class="img-fluid" style="max-height:40px">
                </div>

                <div class="col-12 col-md-4 text-center mb-2">
                    <p class="mb-0 text-sm">&copy; 2025 Gian Hàng Xanh. All rights reserved.</p>
                </div>

                <div class="col-12 col-md-4 text-center text-md-end">
                    <a href="#" class="me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="me-2"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-envelope"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>

</html>
