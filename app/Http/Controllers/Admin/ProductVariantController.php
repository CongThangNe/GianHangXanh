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
    $variant = ProductVariant::findOrFail($id);
    $variant->values()->detach();
    $variant->delete();

    return back()->with('success', 'Đã xoá biến thể.');
}
    // Danh sách biến thể (có thể lọc theo product_id)
    public function apiIndex(Request $request)
    {
        $query = ProductVariant::query();

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $variants = $query->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách biến thể thành công',
            'data' => $variants
        ]);
    }

    // Chi tiết biến thể
    public function apiShow($id)
    {
        $variant = ProductVariant::find($id);

        if (!$variant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy biến thể',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy chi tiết biến thể thành công',
            'data' => $variant
        ]);
    }

    // Tạo biến thể mới
    public function apiStore(Request $request)
    {
        $variant = ProductVariant::create([
            'product_id' => $request->product_id,
            'sku' => $request->sku,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $request->image,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Thêm biến thể thành công',
            'data' => $variant
        ]);
    }

    // Cập nhật biến thể
    public function apiUpdate(Request $request, $id)
    {
        $variant = ProductVariant::find($id);

        if (!$variant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy biến thể',
                'data' => null
            ], 404);
        }

        $variant->update([
            'product_id' => $request->product_id ?? $variant->product_id,
            'sku' => $request->sku ?? $variant->sku,
            'price' => $request->price ?? $variant->price,
            'stock' => $request->stock ?? $variant->stock,
            'image' => $request->image ?? $variant->image,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật biến thể thành công',
            'data' => $variant
        ]);
    }

    // Xóa biến thể
    public function apiDelete($id)
    {
        $variant = ProductVariant::find($id);

        if (!$variant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy biến thể',
                'data' => null
            ], 404);
        }

        $variant->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Xóa biến thể thành công',
            'data' => null
        ]);
    }
}

