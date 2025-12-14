@extends('layouts.admin')
@section('title','Sửa Banner')
@section('content')
<div class="container">
    <h1>Sửa Banner</h1>
    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.banners.form')
    </form>
</div>
@endsection
