<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gian Hàng Xanh')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container">
    <a class="navbar-brand" href="{{ url('/') }}">Gian Hàng Xanh</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}">Giỏ hàng</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.products.index') }}">Admin</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.categories.index') }}">Danh mục</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
    @yield('content')
</div>
</body>
</html>
