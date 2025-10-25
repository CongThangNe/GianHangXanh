@extends('layouts.app')
@section('content')
<div class="container">
    <h3>🎟️ Danh sách mã giảm giá</h3>

    <form method="GET" class="d-flex mb-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm mã..." class="form-control me-2">
        <button class="btn btn-outline-primary">Tìm</button>
        <a href="{{ route('admin.discounts.create') }}" class="btn btn-success ms-3">+ Thêm mới</a>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr class="text-center">
                <th>ID</th>
                <th>Mã</th>
                <th>Giảm</th>
                <th>Giới hạn</th>
                <th>Đã dùng</th>
                <th>Thời gian</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        @forelse($discounts as $item)
            <tr class="text-center">
                <td>{{ $item->id }}</td>
                <td><strong>{{ $item->code }}</strong></td>
                <td>
                    @if($item->percentage)
                        {{ $item->percentage }}%
                    @else
                        {{ number_format($item->fixed_amount) }}đ
                    @endif
                </td>
                <td>{{ $item->usage_limit ?? '∞' }}</td>
                <td>{{ $item->used_count }}</td>
                <td>
                    {{ optional($item->starts_at)->format('d/m/Y H:i') ?? '-' }}<br>
                    {{ optional($item->ends_at)->format('d/m/Y H:i') ?? '-' }}
                </td>
                <td>
                    @if($item->isValid())
                        <span class="badge bg-success">Hiệu lực</span>
                    @else
                        <span class="badge bg-secondary">Hết hạn</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.discounts.edit', $item) }}" class="btn btn-sm btn-primary">Sửa</a>
                    <form action="{{ route('admin.discounts.destroy', $item) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Bạn chắc chắn muốn xóa mã này?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Xóa</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted">Không có mã giảm giá nào.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{ $discounts->links() }}
</div>
@endsection
