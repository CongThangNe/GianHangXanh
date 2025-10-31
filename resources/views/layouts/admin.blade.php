<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin Dashboard')</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root { --brand: #2f8f3a; --sidebar-width: 260px; }

    body { font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, Arial; background: #f9fdf9; }

    /* Sidebar */
    .sidebar { width: var(--sidebar-width); min-height: 100vh; background: linear-gradient(180deg,#fff,#f7fbf7); border-right:1px solid #e6efe6; padding:1rem 0.5rem; position:fixed; top:0; left:0; z-index:1000; }
    .sidebar .nav-link { color:#233; padding:.65rem 1rem; border-radius:.5rem; transition:.2s; }
    .sidebar .nav-link:hover { background: rgba(47,143,58,.05); color: var(--brand); }
    .sidebar .nav-link.active { background: rgba(47,143,58,.1); color: var(--brand); font-weight:600; }

    /* Content */
    .content-area { margin-left: var(--sidebar-width); min-height: 100vh; background:#f8faf8; padding-top:1rem; }

    @media (max-width:991px){
      .sidebar { left:-100%; transition:left .25s ease; }
      .sidebar.show { left:0; }
      .content-area { margin-left:0; }
    }

    .form-required::after { content: " *"; color:#d00; }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <nav class="sidebar">
    <div class="text-center mb-4">
      <h5 class="text-success fw-bold">Gian hàng xanh</h5>
    </div>
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">📊 Dashboard</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">📦 Sản phẩm</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">📁 Danh mục</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">🧾 Đơn hàng</a></li>
      <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.product_variants.*') ? 'active' : '' }}" href="{{ route('admin.product_variants.index') }}">🎛️ Biến thể</a></li>
      
    </ul>
  </nav>

  <!-- Main content -->
  <main class="content-area" id="content-area">
    @yield('content')
  </main>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <script>
  $(document).ready(function() {
    // SPA menu
    $('.sidebar .nav-link').click(function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        $('.sidebar .nav-link').removeClass('active');
        $(this).addClass('active');
        $.get(url, function(data){
            var newContent = $(data).find('#content-area').length ? $(data).find('#content-area').html() : data;
            $('#content-area').html(newContent);
        });
    });

    // SPA pagination
    $(document).on('click', '#content-area .pagination a', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        $.get(url, function(data){
            var newContent = $(data).find('#content-area').length ? $(data).find('#content-area').html() : data;
            $('#content-area').html(newContent);
        });
    });
  });
  </script>
  @stack('scripts')
</body>
</html>
