@extends('layouts.app')
@section('title', 'Kết quả tìm kiếm')
@section('content')

<div class="row g-4 mt-3">
    @forelse($products as $p)
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            @if($p->image)
            <img src="{{ asset('storage/' . $p->image) }}" class="card-img-top" alt="{{ $p->name }}" style="height:200px; object-fit:cover;">
            @endif
            <div class="card-body d-flex flex-column">
                <a href="{{ route('product.show', $p->id) }}" class="product-link">
                    <h6 class="card-title fw-bold">{{ $p->name }}</h6>
                </a>
                <p class="text-success fw-bold mb-2">{{ number_format($p->price,0,',','.') }}₫</p>
                <div class="mt-auto">
                    <a href="{{ route('product.show', $p->id) }}" class="btn btn-outline-success btn-sm w-100">Xem chi tiết sp </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <p>Không tìm thấy sản phẩm nào.</p>
    @endforelse
</div>

<div class="mt-4">
    {{ $products->links() }}
</div>
@endsection