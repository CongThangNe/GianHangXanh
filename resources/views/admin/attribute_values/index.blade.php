@extends('layouts.admin')
@section('title','Quản lý Giá trị thuộc tính')

@section('content')
<h3 class="mb-3">Giá trị thuộc tính</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('admin.attribute_values.create') }}" class="btn btn-primary mb-3">Thêm giá trị</a>

@php
$grouped = $values->groupBy(function($item) {
    return $item->attribute->name;
});
@endphp

@foreach($grouped as $attributeName => $items)

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>{{ $attributeName }}</strong>

        <a 
            href="{{ route('admin.attribute_values.create') }}?attribute={{ $items->first()->attribute_id }}" 
            class="btn btn-success btn-sm">
            + Thêm giá trị cho {{ strtolower($attributeName) }}
        </a>
    </div>

    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>Giá trị</th>
                    <th style="width: 150px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $v)
                <tr>
                    <td>{{ $v->id }}</td>
                    <td>{{ $v->value }}</td>
                    <td>
                        <a href="{{ route('admin.attribute_values.edit', $v->id) }}" class="btn btn-sm btn-warning">Sửa</a>

                        <form method="POST" action="{{ route('admin.attribute_values.destroy', $v->id) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa giá trị này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endforeach
@endsection
