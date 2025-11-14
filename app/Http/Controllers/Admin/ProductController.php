<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ProductVariant;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $attributes = Attribute::with('values')->get();
        return view('admin.products.create', compact('categories', 'attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id'
        ]);

        $data = $request->only(['name', 'description', 'price', 'stock', 'category_id']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/products', 'public');
            $data['image'] = $path;
        }

        $product = Product::create($data);

        //  Lưu biến thể (nếu có)
        if ($request->has('variants')) {
            foreach ($request->input('variants', []) as $variantData) {
                $skuInput = $variantData['sku'] ?? null;
                $skuFinal = $this->makeUniqueSku($skuInput, $product->id);

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $skuFinal,
                    'price' => $variantData['price'] ?? 0,
                    'stock' => $variantData['stock'] ?? 0,
                ]);

                if (!empty($variantData['value_ids'])) {
                    $variant->values()->attach($variantData['value_ids']);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công');
    }

    public function edit($id)
    {
        $product = Product::with('variants.values')->findOrFail($id);
        $categories = Category::all();
        $attributes = Attribute::with('values')->get();
        return view('admin.products.edit', compact('product', 'categories', 'attributes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id'
        ]);

        $product = Product::findOrFail($id);
        $data = $request->only(['name', 'description', 'price', 'stock', 'category_id']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/products', 'public');
            $data['image'] = $path;
        }

        $product->update($data);

        // Cập nhật biến thể
        $product->variants()->delete();
        if ($request->has('variants')) {
            foreach ($request->input('variants', []) as $variantData) {
                $skuInput = $variantData['sku'] ?? null;
                $skuFinal = $this->makeUniqueSku($skuInput, $product->id);

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $skuFinal,
                    'price' => $variantData['price'] ?? 0,
                    'stock' => $variantData['stock'] ?? 0,
                ]);

                if (!empty($variantData['value_ids'])) {
                    $variant->values()->attach($variantData['value_ids']);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->variants()->delete();
        $product->delete();
        return back()->with('success', 'Đã xóa sản phẩm');
    }

    /**
     *  Hàm tạo SKU duy nhất (tự thêm -1, -2, ... nếu trùng)
     */
    private function makeUniqueSku(?string $baseSku, int $productId): string
    {
        $base = $baseSku ?: 'SKU-' . strtoupper(Str::random(6));
        $sku = $base;
        $count = 1;

        while (ProductVariant::where('sku', $sku)->where('product_id', $productId)->exists()) {
            $sku = $base . '-' . $count;
            $count++;
        }

        return $sku;
    }
}
