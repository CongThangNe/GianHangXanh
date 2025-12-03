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

        .navbar-brand {
            font-weight: bold;
            font-size: 1.4rem;
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
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #fff176;
        }

        .product-link {
            color: black !important;
            text-decoration: none;
        }

        .product-link:hover {
            color: green !important;
        }

        footer img {
            transition: transform 0.3s ease;
        }

        footer img:hover {
            transform: scale(1.05);
        }

        /* Dropdown styles */
        .dropdown-menu {
            display: none;
        }

        .dropdown-menu.show {
            display: block;
        }

        #banner-slideshow .slide {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        #banner-slideshow .slide.active {
            opacity: 1;
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
                            <div class="size-6 text-primary">
                                <svg fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z">
                                    </path>
                                </svg>
                            </div>
                            <a class="text-xl font-bold leading-tight tracking-tight" href="{{ url('/') }}">
                                <h2>Gian Hàng Xanh</h2>
                            </a>
                        </div>

                        <!-- Desktop Navigation -->
                        <nav class="hidden md:flex items-center gap-6">
                            <a class="text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary transition-colors"
                                href="{{ url('/') }}">Trang chủ</a>
                            <a class="text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary transition-colors"
                                href="{{ route('cart.index') }}">Giỏ hàng</a>
                            <a class="text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary transition-colors"
                                href="{{ route('admin.dashboard') }}">Admin</a>
                            <a class="text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary transition-colors"
                                href="{{ route('user.orders.index') }}">User's order list</a>
                        </nav>
                    </div>

                    <!-- Right: Search & Auth -->
                    <div class="flex flex-1 justify-end items-center gap-4">

                        <!-- Search Bar -->
                        <form action="{{ route('home') }}" method="GET"
                            class="hidden sm:flex flex-col min-w-40 !h-10 max-w-64">
                            <div class="flex w-full flex-1 items-stretch rounded-lg h-full">
                                <button type="submit"
                                    class="text-white flex bg-green-100/50 items-center justify-center pl-3 pr-2 rounded-l-lg border border-r-0 border-border-light dark:border-border-dark">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">Search</span>
                                </button>
                                <input type="search" name="keyword"
                                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-r-lg text-sm text-white focus:outline-0 focus:ring-0 border border-l-0 border-border-light dark:border-border-dark bg-green-100/50 focus:border-border-light dark:focus:border-border-dark h-full placeholder-white font-normal leading-normal px-3"
                                    placeholder="Tìm kiếm sản phẩm..." value="{{ request('keyword') }}" />
                            </div>
                        </form>

                        <!-- Auth Section -->
                        @guest
                            <div class="hidden md:flex items-center gap-3">
                                <a class="text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary transition-colors"
                                    href="{{ route('login') }}">
                                    Đăng nhập
                                </a>

                                <a class="text-sm font-medium leading-normal hover:text-primary dark:hover:text-primary transition-colors"
                                    href="{{ route('register') }}">
                                    Đăng ký
                                </a>
                            </div>
                        @else
                            <!-- User Dropdown -->
                            <div class="hidden md:block relative" x-data="{ open: false }">
                                <button @click="open = !open" @click.away="open = false"
                                    class="flex items-center gap-2 text-sm font-medium leading-normal text-white hover:text-primary dark:hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">account_circle</span>
                                    <span>{{ Auth::user()->name }}</span>
                                    <span class="material-symbols-outlined" style="font-size: 16px;">expand_more</span>
                                </button>

                                <div x-show="open" x-transition
                                    class="absolute right-0 mt-2 w-48 bg-green-200 border border-border-light dark:border-border-dark rounded-lg shadow-lg py-1">
                                    <a href="{{ route('profile.show') }}"
                                        class="block px-4 py-2 text-sm hover:bg-green-300 transition-colors">Hồ sơ cá
                                        nhân</a>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm hover:bg-green-300 transition-colors">Đơn hàng</a>
                                    <hr class="my-1 border-border-light dark:border-border-dark">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-green-300 transition-colors">Đăng
                                            xuất</button>
                                    </form>
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

                <!-- Logo dự án -->
                <div class="col-12 col-md-4 text-center text-md-start mb-2 mb-md-0">
                    <img src="https://via.placeholder.com/100x40?text=LOGO" alt="Logo dự án" class="img-fluid"
                        style="max-height: 40px;">
                </div>

                <!-- Nội dung -->
                <div class="col-12 col-md-4 text-center mb-2 mb-md-0">
                    <p class="mb-0 text-sm">&copy; 2025 Gian Hàng Xanh. All rights reserved.</p>
                </div>

                <!-- Mạng xã hội -->
                <div class="col-12 col-md-4 text-center text-md-end">
                    <a href="#" class="me-2"><i class="bi bi-facebook" style="font-size: 1.2rem;"></i></a>
                    <a href="#" class="me-2"><i class="bi bi-instagram" style="font-size: 1.2rem;"></i></a>
                    <a href="#"><i class="bi bi-envelope" style="font-size: 1.2rem;"></i></a>
                </div>

            </div>
        </div>
    </footer>



    <!-- Alpine.js for dropdown functionality -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        const slides = document.querySelectorAll('#banner-slideshow .slide');
        let current = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (i === index) slide.classList.add('active');
            });
        }

        showSlide(current);

        setInterval(() => {
            current = (current + 1) % slides.length;
            showSlide(current);
        }, 4000);
    </script>
</body>

</html>
