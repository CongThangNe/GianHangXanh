@extends('layouts.app')
@section('title','Trang chủ')

@section('content')
<style>
/* --- CSS cho sidebar danh mục --- */
.category-sidebar {
    background-color: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}
.category-sidebar h5 {
    color: #0d6efd;
    font-weight: 600;
    text-align: center;
    margin-bottom: 15px;
}
.category-sidebar .list-group-item {
    border: none;
    border-radius: 8px;
    margin-bottom: 8px;
    transition: all 0.2s ease;
}
.category-sidebar .list-group-item a {
    text-decoration: none;
    color: #333;
    display: block;
    font-weight: 500;
}
.category-sidebar .list-group-item:hover {
    background-color: #0d6efd;
}
.category-sidebar .list-group-item:hover a {
    color: #fff;
}
</style>

<div class="row mb-3">
    <!-- Cột danh mục -->
    <div class="col-md-3">
        <div class="category-sidebar">
            <h5>Danh mục</h5>
            <ul class="list-group">
                @foreach($categories as $c)
                <li class="list-group-item">
                    <a href="{{ url('category/'.$c->id) }}">{{ $c->name }}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Cột sản phẩm -->
    <div class="col-md-9">
        <h4 class="mb-3">Sản phẩm</h4>
        <div class="row">
            @foreach($products as $p)
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    @if($p->image)
                        <img src='{{ asset("storage/products/".$p->image) }}' class="card-img-top" alt="{{ $p->name }}">
                    @else
                        <img src="https://picsum.photos/seed/{{ $p->id }}/600/400" class="card-img-top" alt="{{ $p->name }}">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $p->name }}</h5>
                        @if($p->discount && $p->discount > 0)
                            <p class="small text-danger mb-1">Giảm: {{ $p->discount }}%</p>
                        @endif
                        <p class="card-text text-success fw-bold">{{ number_format($p->price,0,',','.') }}₫</p>
                        <p class="card-text small text-muted">Danh mục: {{ $p->category->name ?? '' }}</p>
                        @if(isset($p->status) && $p->status != 'active')
                            <p class="small text-warning">Trạng thái: {{ $p->status }}</p>
                        @endif
                        <a href="{{ url('product/'.$p->id) }}" class="btn btn-primary mt-auto">Xem chi tiết</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
