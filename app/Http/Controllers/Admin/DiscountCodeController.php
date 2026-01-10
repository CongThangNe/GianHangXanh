<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
   use Illuminate\Validation\Rule;
use App\Models\DiscountCode;
use Illuminate\Support\Facades\DB;

class DiscountCodeController extends Controller
{
    public function index()
    {
        $discountCodes = DiscountCode::orderByDesc('id')->paginate(15);
        return view('admin.discount_codes.index', compact('discountCodes'));
    }

    public function create()
    {
        return view('admin.discount_codes.create');
    }

   public function store(Request $request)
{
    $data = $request->validate([
        'code' => 'required|string|max:50|unique:discount_codes,code',
        'type' => 'required|in:percent,value',
        'value' => 'required|integer|min:1',
        'max_discount_value' => 'nullable|integer|min:1',
        'max_uses' => 'required|integer|min:0',
        'starts_at' => 'nullable|date',
        'expires_at' => 'nullable|date|after_or_equal:starts_at',
        'active' => 'boolean',
    ]);

    if ($data['type'] === 'percent') {
        if ($data['value'] > 100) {
            return back()->withErrors(['value' => 'Giảm % không được > 100']);
        }

        if (empty($data['max_discount_value'])) {
            return back()->withErrors([
                'max_discount_value' => 'Giảm % bắt buộc có giảm tối đa'
            ]);
        }
    }

    if ($data['type'] === 'value') {
        $data['max_discount_value'] = null;
    }

    $data['active'] = $request->boolean('active');

    DiscountCode::create($data);

    return redirect()
        ->route('admin.discount-codes.index')
        ->with('success', 'Tạo mã giảm giá thành công');
}



    public function edit(DiscountCode $discountCode)
    {
        return view('admin.discount_codes.edit', compact('discountCode'));
    }



public function update(Request $request, DiscountCode $discountCode)
{
    $data = $request->validate([
        'code' => [
            'required',
            'string',
            'max:50',
            Rule::unique('discount_codes', 'code')->ignore($discountCode->id),
        ],
        'type' => ['required', Rule::in(['percent', 'value'])],
        'value' => ['required', 'integer', 'min:1'],
        'max_discount_value' => ['nullable', 'integer', 'min:0'],
        'max_uses' => ['required', 'integer', 'min:0'],
        'starts_at' => ['nullable', 'date'],
        'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
    ]);

    // ép active luôn có giá trị
    $data['active'] = $request->has('active') ? 1 : 0;

    // nếu là tiền → không có max %
    if ($data['type'] === 'value') {
        $data['max_discount_value'] = null;
    }

    $discountCode->update($data);

    return redirect()
        ->route('admin.discount-codes.index')
        ->with('success', 'Cập nhật mã giảm giá thành công');
}



    public function destroy(DiscountCode $discountCode)
    {
        $discountCode->delete();

        return back()->with('success', 'Đã xóa mã giảm giá');
    }
}