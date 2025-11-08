<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductVariantController extends Controller
{
    public function index()
    {
        $variants = ProductVariant::with('product')->latest()->paginate(10);
        return view('admin.product_variants.index', compact('variants'));
    }

    public function edit($id)
    {
        $variant = ProductVariant::with('attributeValues')->findOrFail($id);
        $products = Product::orderBy('name')->get();
        $attributes = Attribute::with('values')->get();
        $selectedValues = $variant->attributeValues->pluck('id')->toArray();

        return view('admin.product_variants.edit', compact(
            'variant',
            'products',
            'attributes',
            'selectedValues'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'sku' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $variant = ProductVariant::findOrFail($id);

        DB::beginTransaction();
        try {
            // Cập nhật thông tin chính
            $variant->update([
                'product_id' => $request->product_id,
                'sku' => $request->sku,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);

            // Lấy danh sách giá trị thuộc tính
            $attributeValueIds = collect($request->input('attributes', []))
                ->flatten()
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            // Cập nhật bảng trung gian
            $variant->attributeValues()->sync($attributeValueIds);

            DB::commit();
            return redirect()
                ->route('admin.product_variants.index')
                ->with('success', 'Cập nhật biến thể sản phẩm thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->delete();
        return back()->with('success', 'Đã xóa biến thể sản phẩm');
    }
}
