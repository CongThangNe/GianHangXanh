<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category; 
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::withCount('variants')->latest('id')->paginate(12);
        return view('admin.products.index', compact('products'));
    }

    public function create()
{
    $attributes = Attribute::with('values')->orderBy('name')->get();
    $categories = Category::orderBy('name')->get(); // thêm
    return view('admin.products.create', compact('attributes','categories')); // đổi
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'image', 'max:2048'],
            'variants' => ['array'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.value_ids' => ['required', 'array', 'min:1'],
            'variants.*.value_ids.*' => ['integer'],
        ]);

        DB::transaction(function () use ($request, $data) {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('uploads/products', 'public');
            }

            $product = Product::create([
                'name' => $data['name'],
                'category_id' => $data['category_id'] ?? null,
                'price' => $data['price'],
                'description' => $data['description'] ?? null,
                'image' => $imagePath,
            ]);

            $this->upsertVariants($product, $request->input('variants', []), false);
        });

        return redirect()->route('admin.products.index')->with('success', 'Tạo sản phẩm thành công.');
    }

    public function edit($id)
{
    $product    = Product::with(['variants.values.attribute'])->findOrFail($id);
    $attributes = Attribute::with('values')->orderBy('name')->get();
    $categories = Category::orderBy('name')->get(); // thêm
    return view('admin.products.edit', compact('product','attributes','categories')); // đổi
}

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'image', 'max:2048'],
            'variants' => ['array'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.value_ids' => ['required', 'array', 'min:1'],
            'variants.*.value_ids.*' => ['integer'],
        ]);

        DB::transaction(function () use ($request, $data, $product) {
            $imagePath = $product->image;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('uploads/products', 'public');
            }

            $product->update([
                'name' => $data['name'],
                'category_id' => $data['category_id'] ?? null,
                'price' => $data['price'],
                'description' => $data['description'] ?? null,
                'image' => $imagePath,
            ]);

            $this->upsertVariants($product, $request->input('variants', []), true);
        });

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        DB::transaction(function () use ($product) {
            foreach ($product->variants as $v) {
                $v->values()->detach();
                $v->delete();
            }
            $product->delete();
        });

        return back()->with('success', 'Đã xóa sản phẩm.');
    }

    protected function upsertVariants(Product $product, array $variantsPayload, bool $isUpdate): void
    {
        $normalizeKey = function (array $valueIds) {
            $ids = array_map('intval', $valueIds);
            sort($ids);
            return implode('-', $ids);
        };

        $existing = $product->variants()->with('values')->get();
        $existingByKey = [];
        foreach ($existing as $v) {
            $key = $normalizeKey($v->values->pluck('id')->all());
            $existingByKey[$key] = $v;
        }

        $seenKeys = [];

        foreach ($variantsPayload as $row) {
            $valueIds = $row['value_ids'] ?? [];
            if (empty($valueIds)) continue;

            $key = $normalizeKey($valueIds);
            $seenKeys[] = $key;

            $payload = [
                'price' => $row['price'],
                'stock' => $row['stock'],
                'sku' => $row['sku'] ?: $this->makeSkuFromValues($valueIds),
            ];

            if (isset($existingByKey[$key])) {
                $variant = $existingByKey[$key];
                $variant->update($payload);
                $variant->values()->sync($valueIds);
            } else {
                $variant = $product->variants()->create($payload);
                $variant->values()->sync($valueIds);
            }
        }

        if ($isUpdate) {
            foreach ($existingByKey as $key => $variant) {
                if (!in_array($key, $seenKeys, true)) {
                    $variant->values()->detach();
                    $variant->delete();
                }
            }
        }
    }

    protected function makeSkuFromValues(array $valueIds): string
    {
        $values = AttributeValue::with('attribute')
            ->whereIn('id', $valueIds)
            ->get()
            ->sortBy(fn($v) => [$v->attribute->id, $v->id]);

        $parts = $values->map(function ($v) {
            $slug = Str::upper(Str::slug($v->value, '-'));
            return $slug;
        })->all();

        return implode('-', $parts);
    }
     // Lấy danh sách sản phẩm
    public function apiIndex()
    {
        $products = Product::withCount('variants')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách sản phẩm thành công',
            'data' => $products
        ]);
    }

    // Lấy chi tiết sản phẩm + biến thể
    public function apiShow($id)
    {
        $product = Product::with('variants')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy sản phẩm',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy chi tiết sản phẩm thành công',
            'data' => $product
        ]);
    }

    // Thêm sản phẩm mới
    public function apiStore(Request $request)
    {
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $request->image,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Thêm sản phẩm thành công',
            'data' => $product
        ]);
    }

    // Cập nhật sản phẩm
    public function apiUpdate(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy sản phẩm',
                'data' => null
            ], 404);
        }

        $product->update([
            'category_id' => $request->category_id ?? $product->category_id,
            'name' => $request->name ?? $product->name,
            'description' => $request->description ?? $product->description,
            'price' => $request->price ?? $product->price,
            'image' => $request->image ?? $product->image,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật sản phẩm thành công',
            'data' => $product
        ]);
    }

    // Xóa sản phẩm (và biến thể liên quan)
    public function apiDelete($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy sản phẩm',
                'data' => null
            ], 404);
        }

        // Xóa biến thể trước
        ProductVariant::where('product_id', $product->id)->delete();
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Xóa sản phẩm thành công',
            'data' => null
        ]);
    }
    public function search(Request $request)
    {
        $keyword = $request->keyword;

        $products = Product::where('name', 'LIKE', '%' . $keyword . '%')
            ->orWhere('description', 'LIKE', '%' . $keyword . '%')
            ->paginate(12);

        $categories = Category::all();

        return view('search.search', compact('products', 'categories', 'keyword'));
    }
}
