@extends('layouts.admin')
@section('title', 'Danh sách mã giảm giá')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between mb-3">
        <h3>Danh sách mã giảm giá</h3>
        <a href="{{ route('admin.discount-codes.create') }}" class="btn btn-success">Thêm mã giảm giá</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Mã</th>
                    <th>Loại</th>
                    <th>Giá trị</th>
                    <th>Bắt đầu</th>
                    <th>Hết hạn</th>
                    <th>Lượt dùng</th>
                    <th>Tối đa</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @php use Illuminate\Support\Carbon; @endphp
                @forelse ($discountCodes as $d)
                <tr>
                    <td>{{ $d->id }}</td>
                    <td><strong>{{ $d->code }}</strong></td>

                    {{-- Loại --}}
                    <td>
                        @if ($d->type === 'percent')
                            <span class="badge bg-info text-dark">%</span>
                        @else
                            <span class="badge bg-warning text-dark">VND</span>
                        @endif
                    </td>

                    {{-- Giá trị --}}
                    <td>
                        @if ($d->type === 'percent')
                            {{ rtrim(rtrim(number_format($d->discount_percent, 2), '0'), '.') }}%
                        @else
                            {{ number_format($d->discount_value, 0, ',', '.') }}đ
                        @endif
                    </td>

                    {{-- Ngày bắt đầu --}}
                    <td>
                        {{ $d->starts_at ? Carbon::parse($d->starts_at)->format('d/m/Y') : '-' }}
                    </td>

                    {{-- Ngày hết hạn --}}
                    <td>
                        {{ $d->expires_at ? Carbon::parse($d->expires_at)->format('d/m/Y') : '-' }}
                    </td>

                    {{-- Lượt dùng --}}
                    <td>{{ $d->used_count ?? 0 }}</td>

                    {{-- Giới hạn sử dụng --}}
                    <td>
                        {{ $d->max_uses == 0 ? '∞' : $d->max_uses }}
                    </td>

                    {{-- Hành động --}}
                    <td>
                        <a href="{{ route('admin.discount-codes.edit', $d->id) }}" class="btn btn-sm btn-primary">Sửa</a>
                        <form action="{{ route('admin.discount-codes.destroy', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa mã này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">Chưa có mã giảm giá nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
