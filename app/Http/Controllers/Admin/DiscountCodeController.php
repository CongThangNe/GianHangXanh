<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiscountCode; // Sử dụng Model mới

class DiscountCodeController extends Controller
{
    public function index()
    {
        $discountCodes = DiscountCode::latest()->paginate(10);
        return view('admin.discount_codes.index', compact('discountCodes'));
    }

    public function create()
    {
        return view('admin.discount_codes.create');
    }

    public function store(Request $request)
    {
        // 1. Validate: Không trùng code & Giá trị hợp lệ
        $request->validate([
            'code' => 'required|unique:discount_codes,code|max:255',
            'type' => 'required|in:percent,value', // Giả định có thêm trường type
            'value' => 'required|numeric|min:1',
            'expires_at' => 'nullable|date|after:today', // Hết hạn phải sau ngày hôm nay
            'max_uses' => 'nullable|integer|min:1',
        ]);

        $data = $request->only('code', 'expires_at', 'max_uses');

        // Xử lý giá trị giảm giá theo loại
        if ($request->type === 'percent') {
            $request->validate(['value' => 'max:99.99']); // Giảm theo % (0 < value < 100)
            $data['discount_percent'] = $request->value;
            // Giả định discount_value = 0 nếu là %
            $data['discount_value'] = 0; 
        } else { // type === 'value' (Giảm trực tiếp)
            $data['discount_percent'] = 0;
            // Giả định bạn có cột discount_value trong DB
            $data['discount_value'] = $request->value;
        }
        
        // Thêm trường 'max_uses' vào DB nếu bạn muốn lưu giới hạn sử dụng
        $data['max_uses'] = $request->max_uses ?? 0; // 0: không giới hạn

        DiscountCode::create($data);

        return redirect()->route('admin.discount-codes.index')->with('success', 'Thêm mã giảm giá thành công! 🎟️');
    }

    public function edit(DiscountCode $discountCode)
    {
        // Lấy type và value hiện tại để đổ vào form
        $discountCode->type = $discountCode->discount_percent > 0 ? 'percent' : 'value';
        $discountCode->value = $discountCode->discount_percent > 0 ? $discountCode->discount_percent : $discountCode->discount_value;
        
        return view('admin.discount_codes.edit', compact('discountCode'));
    }

    public function update(Request $request, DiscountCode $discountCode)
    {
        // 1. Validate: Code không trùng (ngoại trừ chính nó) & Giá trị hợp lệ
        $request->validate([
            'code' => 'required|unique:discount_codes,code,' . $discountCode->id . '|max:255',
            'type' => 'required|in:percent,value',
            'value' => 'required|numeric|min:1',
            'expires_at' => 'nullable|date|after:today',
            'max_uses' => 'nullable|integer|min:1',
        ]);
        
        $data = $request->only('code', 'expires_at', 'max_uses');

        // Xử lý giá trị giảm giá theo loại
        if ($request->type === 'percent') {
            $request->validate(['value' => 'max:99.99']); // Giảm theo % (0 < value < 100)
            $data['discount_percent'] = $request->value;
            $data['discount_value'] = 0; 
        } else { // type === 'value' (Giảm trực tiếp)
            $data['discount_percent'] = 0;
            $data['discount_value'] = $request->value;
        }

        $data['max_uses'] = $request->max_uses ?? 0;

        $discountCode->update($data);

        return redirect()->route('admin.discount-codes.index')->with('success', 'Cập nhật mã giảm giá thành công! ✅');
    }

    public function destroy(DiscountCode $discountCode)
    {
        $discountCode->delete();
        return back()->with('success', 'Đã xóa mã giảm giá. 🗑️');
    }
}