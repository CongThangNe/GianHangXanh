<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Facades\DB;

class ProductVariantController extends Controller
{
    public function index()
    {
        $variants = ProductVariant::with(['product', 'attributeValues.attribute'])->paginate(20);
        return view('admin.product_variants.index', compact('variants'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $attributes = Attribute::with('values')->get();
        return view('admin.product_variants.create', compact('products', 'attributes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'sku' => 'required|string|max:100|unique:product_variants,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'attributes' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            // 🔹 Kiểm tra tổ hợp attribute values có bị trùng không
            $existingVariant = DB::table('product_attribute_values')
                ->join('product_variants', 'product_attribute_values.product_variant_id', '=', 'product_variants.id')
                ->where('product_variants.product_id', $data['product_id'])
                ->whereIn('product_attribute_values.attribute_value_id', array_values($data['attributes']))
                ->groupBy('product_variants.id')
                ->havingRaw('COUNT(*) = ?', [count($data['attributes'])])
                ->first();

            if ($existingVariant) {
                return back()->withErrors(['attributes' => 'Biến thể này đã tồn tại cho sản phẩm này.'])->withInput();
            }

            // 🔹 Tạo biến thể
            $variant = ProductVariant::create([
                'product_id' => $data['product_id'],
                'sku' => $data['sku'],
                'price' => $data['price'],
                'stock' => $data['stock'],
            ]);

            // 🔹 Lưu vào bảng trung gian product_attribute_values
            foreach ($data['attributes'] as $attrId => $valueId) {
                DB::table('product_attribute_values')->insert([
                    'product_variant_id' => $variant->id,
                    'attribute_value_id' => $valueId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('admin.product_variants.index')->with('success', 'Thêm biến thể thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Lỗi khi lưu: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $variant = ProductVariant::with('attributeValues')->findOrFail($id);
        $products = Product::orderBy('name')->get();
        $attributes = Attribute::with('values')->get();
        $selectedValues = $variant->attributeValues->pluck('id')->toArray();

        return view('admin.product_variants.edit', compact('variant', 'products', 'attributes', 'selectedValues'));
    }

    public function update(Request $request, $id)
    {
        $variant = ProductVariant::findOrFail($id);

        $data = $request->validate([
            'sku' => 'required|string|max:100|unique:product_variants,sku,' . $id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'attributes' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $variant->update([
                'sku' => $data['sku'],
                'price' => $data['price'],
                'stock' => $data['stock'],
            ]);

            // Cập nhật lại attribute values
            DB::table('product_attribute_values')->where('product_variant_id', $variant->id)->delete();

            foreach ($data['attributes'] as $attrId => $valueId) {
                DB::table('product_attribute_values')->insert([
                    'product_variant_id' => $variant->id,
                    'attribute_value_id' => $valueId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('admin.product_variants.index')->with('success', 'Cập nhật biến thể thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Lỗi khi cập nhật: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::table('product_attribute_values')->where('product_variant_id', $id)->delete();
        ProductVariant::destroy($id);
        return redirect()->route('admin.product_variants.index')->with('success', 'Xóa biến thể thành công');
    }
}
