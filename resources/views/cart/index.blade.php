@extends('layouts.app')
@section('title','Giỏ hàng')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Giỏ hàng của bạn</h3>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
    @endif

    @if(!$cart || $cart->items->isEmpty())
    <div class="alert alert-info">
        Hiện tại giỏ hàng trống. Hãy thêm sản phẩm vào giỏ.
    </div>
    @else
    @php
    $total = 0;
    @endphp
    <div class="table-responsive mb-4">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Biến thể</th>
                    <th class="text-end">Giá</th>
                    <th class="text-center" style="width: 150px;">Số lượng</th>
                    <th class="text-end">Thành tiền</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart->items as $item)
                @php
                $variant = $item->variant;
                $product = $variant->product ?? null;
                $lineTotal = $item->price * $item->quantity;
                $total += $lineTotal;
                @endphp
                <tr>
                    <td>
                        @if($product)
                        <strong>{{ $product->name }}</strong>
                        @else
                        <em>Sản phẩm không tồn tại</em>
                        @endif
                    </td>
                    <td>
                        @if($variant)
                        {{ $variant->attributeValues->pluck('value')->join(' / ') }}
                        @endif
                    </td>
                    <td class="text-end">
                        {{ number_format($item->price, 0, ',', '.') }}₫
                    </td>
                    <td class="text-center">
                        <form action="{{ route('cart.update') }}" method="POST" class="d-inline-flex">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <input type="number"
                                name="quantity"
                                value="{{ $item->quantity }}"
                                min="1"
                                max="{{ $variant ? $variant->stock : $item->quantity }}"
                                class="form-control form-control-sm text-center me-2"
                                style="width: 70px;">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                Cập nhật
                            </button>
                        </form>
                    </td>
                    <td class="text-end">
                        {{ number_format($lineTotal, 0, ',', '.') }}₫
                    </td>
                    <td class="text-center">
                        <form action="{{ route('cart.remove') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xoá sản phẩm này?');">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                Xoá
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Tổng cộng:</th>
                    <th class="text-end">
                        {{ number_format($total, 0, ',', '.') }}₫
                    </th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <!-- Nút thanh toán -->
    <div class="d-flex justify-content-end">
        <form action="{{ route('checkout.index') }}" method="GET">
            <button type="submit" class="btn btn-success px-4">
                Tiến hành thanh toán
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
