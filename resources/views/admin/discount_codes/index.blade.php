@extends('layouts.app')
@section('title', 'Quản lý mã giảm giá')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Quản lý Mã Giảm Giá 🎁</h3>
    <a href="{{ route('admin.discount-codes.create') }}" class="btn btn-success">Thêm Mã Giảm Giá</a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Loại giảm</th>
            <th>Giá trị giảm</th>
            <th>Đã dùng/Giới hạn</th>
            <th>Hết hạn</th>
            <th width="150">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($discountCodes as $code)
        <tr>
            <td>{{ $code->id }}</td>
            <td class="fw-bold">{{ $code->code }}</td>
            <td>
                @if($code->discount_percent > 0)
                    <span class="badge bg-primary">Giảm %</span>
                @else
                    <span class="badge bg-info">Giảm trực tiếp</span>
                @endif
            </td>
            <td>
                @if($code->discount_percent > 0)
                    {{ $code->discount_percent }}%
                @else
                    {{ number_format($code->discount_value, 0, ',', '.') }}₫
                @endif
            </td>
            <td>
                {{ $code->used_count }}/
                @if($code->max_uses > 0)
                    {{ $code->max_uses }}
                @else
                    <span class="text-muted">Không giới hạn</span>
                @endif
            </td>
            <td>
                @if($code->expires_at && $code->expires_at->isPast())
                    <span class="badge bg-danger">Đã hết hạn</span>
                @else
                    {{ $code->expires_at ? $code->expires_at->format('d/m/Y') : 'Vĩnh viễn' }}
                @endif
            </td>
            <td>
                <a href="{{ route('admin.discount-codes.edit', $code->id) }}" class="btn btn-sm btn-primary">Sửa</a>
                <form action="{{ route('admin.discount-codes.destroy', $code->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa mã giảm giá này?')">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $discountCodes->links() }}
@endsection