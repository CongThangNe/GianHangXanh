<?php
// app/Http/Controllers/Admin/DiscountController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscountRequest;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $query = Discount::query();

        if ($search = $request->get('q')) {
            $query->where('code', 'like', "%$search%");
        }

        $discounts = $query->orderByDesc('id')->paginate(10);
        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(StoreDiscountRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        Discount::create($data);
        return redirect()->route('admin.discounts.index')->with('success', 'Tạo mã giảm giá thành công!');
    }

    public function edit(Discount $discount)
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(StoreDiscountRequest $request, Discount $discount)
    {
        $discount->update($request->validated());
        return redirect()->route('admin.discounts.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return redirect()->route('admin.discounts.index')->with('success', 'Đã xóa mã giảm giá!');
    }
}
