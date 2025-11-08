<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiscountCode;
use Illuminate\Support\Facades\DB;

class DiscountCodeController extends Controller
{
    /**
     * Hiển thị danh sách mã giảm giá
     */
    public function index()
    {
        // Lấy danh sách mã giảm giá, sắp xếp mới nhất
        $discountCodes = DiscountCode::orderByDesc('id')->paginate(10);

        // Trả biến về view
        return view('admin.discount_codes.index', compact('discountCodes'));
    }

    /**
     * Form tạo mới
     */
    public function create()
    {
        return view('admin.discount_codes.create');
    }

    /**
     * Lưu mã giảm giá mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:discount_codes,code',
            'type' => 'required|in:percent,value',
            'value' => [
                'required', 'numeric',
                function($attr, $val, $fail) use ($request) {
                    if ($request->type === 'percent' && ($val <= 0 || $val > 100)) {
                        $fail('Giá trị giảm % phải trong khoảng 1–100.');
                    }
                    if ($request->type === 'value' && $val <= 0) {
                        $fail('Giá trị giảm tiền phải lớn hơn 0.');
                    }
                }
            ],
            'starts_at' => 'required|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'max_uses' => 'nullable|integer|min:0',
            'max_discount_value' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $discount = new DiscountCode();
            $discount->code = $request->input('code');
            $discount->discount_percent = 0;
            $discount->discount_value = 0;

            // Xử lý loại mã
            if ($request->type === 'percent') {
                $discount->discount_percent = $request->input('value');
            } else {
                $discount->discount_value = $request->input('value');
            }

            $discount->max_uses = $request->input('max_uses') ?? 0;
            $discount->used_count = 0;
            $discount->starts_at = $request->input('starts_at');
            $discount->expires_at = $request->input('expires_at');
            $discount->max_discount_value = $request->input('max_discount_value') ?? 0;

            $discount->save();

            DB::commit();
            return redirect()
                ->route('admin.discount-codes.index')
                ->with('success', 'Tạo mã giảm giá thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Form sửa mã giảm giá
     */
    public function edit($id)
{
    $discountCode = \App\Models\DiscountCode::findOrFail($id);
    return view('admin.discount_codes.edit', compact('discountCode'));
}

    /**
     * Cập nhật mã giảm giá
     */
    public function update(Request $request, $id)
    {
        $discount = DiscountCode::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:50|unique:discount_codes,code,' . $discount->id,
            'type' => 'required|in:percent,value',
            'value' => [
                'required', 'numeric',
                function($attr, $val, $fail) use ($request) {
                    if ($request->type === 'percent' && ($val <= 0 || $val > 100)) {
                        $fail('Giá trị giảm % phải trong khoảng 1–100.');
                    }
                    if ($request->type === 'value' && $val <= 0) {
                        $fail('Giá trị giảm tiền phải lớn hơn 0.');
                    }
                }
            ],
            'starts_at' => 'required|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'max_uses' => 'nullable|integer|min:0',
            'max_discount_value' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $discount->code = $request->input('code');
            $discount->discount_percent = 0;
            $discount->discount_value = 0;

            if ($request->type === 'percent') {
                $discount->discount_percent = $request->input('value');
            } else {
                $discount->discount_value = $request->input('value');
            }

            $discount->max_uses = $request->input('max_uses') ?? 0;
            $discount->starts_at = $request->input('starts_at');
            $discount->expires_at = $request->input('expires_at');
            $discount->max_discount_value = $request->input('max_discount_value') ?? 0;

            $discount->save();

            DB::commit();
            return redirect()
                ->route('admin.discount-codes.index')
                ->with('success', 'Cập nhật mã giảm giá thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Xóa mã giảm giá
     */
    public function destroy($id)
    {
        $discount = DiscountCode::findOrFail($id);
        $discount->delete();

        return redirect()
            ->route('admin.discount-codes.index')
            ->with('success', 'Xóa mã giảm giá thành công!');
    }
}
