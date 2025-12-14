@extends('layouts.admin')
@section('title','Thêm Banner')
@section('content')
<div class="container">
    <h1>Thêm Banner</h1>
    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.banners.form')
    </form>
</div>
@endsection
