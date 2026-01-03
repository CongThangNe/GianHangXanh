@extends('layouts.admin')
@section('title', 'Danh sách mã giảm giá')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Danh sách mã giảm giá</h3>

    <a href="{{ route('admin.discount-codes.create') }}"
       class="btn btn-primary">
        + Thêm mã giảm giá
    </a>
</div>

<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>Code</th>
            <th>Loại</th>
            <th>Giá trị</th>
            <th>Lượt dùng</th>
            <th>Thời gian</th>
            <th>Trạng thái</th>
            <th width="120">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @foreach($discountCodes as $code)
            <tr>
                <td>{{ $code['code'] }}</td>
                <td>{{ $code['type'] === 'percent' ? '%' : 'VNĐ' }}</td>
                <td>
                    {{ $code['type'] === 'percent'
                        ? $code['value'].'%'
                        : number_format($code['value']).'đ'
                    }}
                </td>
                <td>{{ $code['used_count'] }} / {{ $code['max_uses'] }}</td>
                <td>
                    {{ optional($code['starts_at'])->format('d/m/Y') ?? '—' }}
                    →
                    {{ optional($code['expires_at'])->format('d/m/Y') ?? '—' }}
                </td>
                <td>
                    @if($code['active'])
                        <span class="badge bg-success">Hoạt động</span>
                    @else
                        <span class="badge bg-secondary">Ngưng</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.discount-codes.edit', $code['id']) }}"
                       class="btn btn-sm btn-warning">Sửa</a>

                    <form action="{{ route('admin.discount-codes.destroy', $code['id']) }}"
                          method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"
                                onclick="return confirm('Xóa mã này?')">
                            Xóa
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection
