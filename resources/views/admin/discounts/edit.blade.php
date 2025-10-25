@extends('layouts.app')
@section('content')
<div class="container">
    <h3>✏️ Sửa mã giảm giá</h3>
    <form action="{{ route('admin.discounts.update', $discount) }}" method="POST">
        @method('PUT')
        @include('admin.discounts._form')
    </form>
</div>
@endsection
