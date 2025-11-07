@extends('layouts.app')
@section('title', 'Giỏ hàng của bạn')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Giỏ hàng của bạn</h3>

    <div id="cartContainer" class="border rounded p-3 bg-light"></div>

    <a href="/" class="btn btn-secondary mt-3">Tiếp tục mua sắm</a>
</div>

<script src="{{ asset('js/cart.js') }}"></script>
@endsection
