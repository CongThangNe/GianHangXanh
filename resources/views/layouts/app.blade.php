<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Gian Hàng Xanh')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
      /* màu vàng khi hover */
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
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="{{ url('/') }}">🌱 Gian Hàng Xanh</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <!-- Menu trái -->
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Trang chủ</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}">Giỏ hàng</a></li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
        </ul>

        <!-- Tìm kiếm -->
        <form class="d-flex me-3" role="search" action="{{ route('search') }}" method="GET">
          <input class="form-control me-2"
            type="search"
            name="keyword"
            placeholder="Tìm kiếm sản phẩm..."
            value="{{ request('keyword') }}">
          <button class="btn btn-light" type="submit">Tìm</button>
        </form>

        <!-- Auth -->
        <ul class="navbar-nav">
          @guest
          <li class="nav-item"><a class="nav-link" href="#">Đăng nhập</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Đăng ký</a></li>
          @else
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              👤 {{ Auth::user()->name }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="#">Hồ sơ cá nhân</a></li>
              <li><a class="dropdown-item" href="#">Đơn hàng</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <form method="POST" action="#">
                  @csrf
                  <button type="submit" class="dropdown-item">Đăng xuất</button>
                </form>
              </li>
            </ul>
          </li>
          @endguest
        </ul>
      </div>
    </div>
  </nav>

  <!-- Content -->
  <div class="container mt-4">
    @yield('content')
  </div>

  <!-- Footer -->
  <footer class="text-center text-md-start">
    <div class="container py-3">
      <div class="row align-items-center">

        <!-- Logo dự án -->
        <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
          <img src="https://via.placeholder.com/120x60?text=LOGO"
            alt="Logo dự án"
            class="img-fluid"
            style="max-height: 60px;">
        </div>

        <!-- Nội dung -->
        <div class="col-md-4 text-center mb-3 mb-md-0">
          <p class="mb-1">&copy; 2025 Gian Hàng Xanh. All rights reserve.</p>
        </div>

        <!-- Mạng xã hội -->
        <div class="col-md-4 text-center text-md-end">
          <a href="#" class="me-3"><i class="bi bi-facebook" style="font-size: 1.4rem;"></i></a>
          <a href="#" class="me-3"><i class="bi bi-instagram" style="font-size: 1.4rem;"></i></a>
          <a href="#"><i class="bi bi-envelope" style="font-size: 1.4rem;"></i></a>
        </div>

      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>