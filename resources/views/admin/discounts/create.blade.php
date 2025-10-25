@extends('layouts.app')
@section('content')
<div class="container">
    <h3>➕ Thêm mã giảm giá</h3>
    <form action="{{ route('admin.discounts.store') }}" method="POST">
        @include('admin.discounts._form')
    </form>
</div>
@endsection
