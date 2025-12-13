<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin Dashboard')</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --brand: #2f8f3a;
      --sidebar-width: 260px;
    }

    /* Reset browser default margin and set font/background */
    body {
      margin: 0;
      font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, Arial;
      background: #f9fdf9;
    }

    /* Sidebar */
    .sidebar {
      width: var(--sidebar-width);
      min-height: 100vh;
      background: linear-gradient(180deg, #fff, #f7fbf7);
      border-right: 1px solid #e6efe6;
      padding: 1rem 0.5rem;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 1000;
    }

    .sidebar .nav-link {
      color: #233;
      padding: .65rem 1rem;
      border-radius: .5rem;
      transition: .2s;
    }

    .sidebar .nav-link:hover {
      background: rgba(47, 143, 58, .05);
      color: var(--brand);
    }

    .sidebar .nav-link.active {
      background: rgba(47, 143, 58, .1);
      color: var(--brand);
      font-weight: 600;
    }

    /* account for the 1px sidebar border */
    .content-area {
      margin-left: calc(var(--sidebar-width) + 1px);
      min-height: 100vh;
      background: #f8faf8;
      padding: 1rem;
    }

    @media (max-width: 991px) {
      .sidebar {
        left: -100%;
        transition: left .25s ease;
        position: fixed;
      }

      .sidebar.show {
        left: 0;
      }

      .content-area {
        margin-left: 0;
        padding-top: 4.5rem;
      }

      .mobile-toggle {
        display: inline-block;
      }
    }

    .mobile-toggle {
      display: none;
      margin-right: .5rem;
    }

    .form-required::after {
      content: " *";
      color: #d00;
    }

    .submenu {
      display: none;
      padding-left: 0;
      margin-top: .25rem;
    }

    .has-submenu .submenu-toggle {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-weight: 500;
    }

    .has-submenu .arrow {
      transition: transform .25s ease;
      font-size: .85rem;
    }

    .has-submenu.open .arrow {
      transform: rotate(90deg);
    }

    .submenu .nav-link {
      padding: .45rem 1rem;
      border-radius: .4rem;
      color: #333;
      font-size: .95rem;
      transition: .2s;
    }

    .submenu .nav-link:hover {
      background: rgba(47, 143, 58, .08);
      color: var(--brand);
    }

    /* Hiển thị submenu khi open */
    .has-submenu.open .submenu {
      display: block;
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <nav class="sidebar" id="sidebar">
    <div class="text-center mb-4">
      <h5 class="text-success fw-bold">Gian hàng xanh</h5>
    </div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">📊 Dashboard</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.products.index') }}">🛍️ Sản phẩm</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.categories.index') }}">📂 Danh mục</a>
      </li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">🧾 Đơn hàng</a></li>
      <!-- ✅ Dropdown Thuộc tính (thêm vào ngay sau Biến thể) -->
      <li class="nav-item has-submenu">
        <a href="#" class="nav-link submenu-toggle">
          🧩 Thuộc tính
          <span class="arrow ms-auto">▸</span>
        </a>
        <ul class="submenu list-unstyled ms-3">
          <li><a class="nav-link" href="{{ route('admin.attributes.index') }}">Danh sách thuộc tính</a></li>
          <li><a class="nav-link" href="{{ route('admin.attribute_values.index') }}">Giá trị thuộc tính</a></li>
        </ul>
      </li>
      <!-- ✅ End Dropdown -->
      <li class="nav-item"><a class="nav-link" href="{{ route('admin.discount-codes.index') }}">🎟️ Mã Giảm Giá</a></li>
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/') }}">🏠 Về trang chủ</a>
      </li>
    </ul>
  </nav>

  <!-- Main content -->
  <main class="content-area" id="content-area">
    <div class="d-flex align-items-center mb-3">
      <button class="btn btn-outline-secondary mobile-toggle" id="sidebarToggle">☰</button>
      <h4 class="m-0">@yield('title')</h4>
    </div>

    @yield('content')
  </main>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <script>
    $('.submenu-toggle').on('click', function(e) {
      e.preventDefault();
      const parent = $(this).closest('.has-submenu');
      parent.toggleClass('open');
    });
  </script>

  @stack('scripts')
</body>

</html>