@extends('layouts.app')
@section('title',$product->name)
@section('content')
<div class="row">
    <div class="col-md-6">
        @if($product->image)
        <img src='{{ asset("storage/products/".$product->image) }}' class="img-fluid" alt="{{ $product->name }}">
        @endif
    </div>
    <div class="col-md-6">
        <h2>{{ $product->name }}</h2>
        <h4 class="text-success">{{ number_format($product->price,0,',','.') }}₫</h4>
        <p>{{ $product->description }}</p>
        <p>Danh mục: {{ $product->category->name ?? '' }}</p>
    </div>
</div>
@endsection
