@extends('layouts.app')
@section('title',$product->name)
@section('content')
<div class="row">
    <div class="col-md-6">
        @if($product->image)
        <img src='{{ asset("storage/products/".$product->image) }}' class="img-fluid" alt="{{ $product->name }}">
        @else
        <img src="https://picsum.photos/seed/{{ $product->id }}/800/600" class="img-fluid" alt="{{ $product->name }}">
        @endif
    </div>
    <div class="col-md-6">
        <h2>{{ $product->name }}</h2>
        @if($product->discount && $product->discount > 0)
        <p class="small text-danger">Giảm: {{ $product->discount }}%</p>
        @endif
        <h4 class="text-success">{{ number_format($product->price,0,',','.') }}₫</h4>
        <p>{{ $product->description }}</p>
        <p>Danh mục: {{ $product->category->name ?? '' }}
        @if(isset($product->status) && $product->status != 'active')
        <p class="small text-warning">Trạng thái: {{ $product->status }}</p>
        @endif</p>
        <a href="{{ url('/') }}" class="btn btn-secondary">Quay lại</a>
    </div>
</div>
@endsection
