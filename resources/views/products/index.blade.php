@extends('layouts.app')
@section('title','Trang chủ')
@section('content')
<div class="row mb-3">
    <div class="col-md-3">
        <h5>Danh mục</h5>
        <ul class="list-group">
            @foreach($categories as $c)
            <li class="list-group-item"><a href="{{ url('category/'.$c->id) }}">{{ $c->name }}</a></li>
            @endforeach
        </ul>
    </div>
    <div class="col-md-9">
        <h4>Sản phẩm</h4>
        <div class="row">
            @foreach($products as $p)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    @if($p->image)
                    <img src='{{ asset("storage/products/".$p->image) }}' class="card-img-top" alt="{{ $p->name }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $p->name }}</h5>
                        <p class="text-success fw-bold">{{ number_format($p->price,0,',','.') }}₫</p>
                        <a href="{{ url('product/'.$p->id) }}" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        {{ $products->links() }}
    </div>
</div>
@endsection
