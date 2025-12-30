<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ProductVariant;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Schema;

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
        $product = Product::with('variants')->findOrFail($id);

        // =====================
        // VALIDATE KHÔNG CHO XÓA SẢN PHẨM ĐÃ PHÁT SINH ĐƠN HÀNG
        // - Nếu đơn hàng ở trạng thái "thành công" (delivered/paid) => CHẶN XÓA
        // - Để an toàn dữ liệu, nếu sản phẩm đã xuất hiện trong bất kỳ đơn nào => CHẶN XÓA
        // =====================

        $variantIds = $product->variants->pluck('id');

        // Một số bản DB của dự án có lưu trực tiếp product_id trong order_details (và product_variant_id có thể null)
        $hasOrderDetailsProductId = Schema::hasColumn('order_details', 'product_id');

        $hasAnyOrder = false;
        $hasSuccessfulOrder = false;

        // --- Check theo product_variant_id (nếu có variants) ---
        if ($variantIds->isNotEmpty()) {
            $hasAnyOrder = OrderDetail::whereIn('product_variant_id', $variantIds)->exists();

            $hasSuccessfulOrder = OrderDetail::whereIn('product_variant_id', $variantIds)
                ->whereHas('order', function ($q) {
                    // "thành công" có thể là giao thành công hoặc đã thanh toán
                    if (Schema::hasColumn('orders', 'delivery_status')) {
                        $q->orWhere('delivery_status', 'delivered');
                    }
                    if (Schema::hasColumn('orders', 'payment_status')) {
                        $q->orWhere('payment_status', 'paid');
                    }
                    // fallback cho cột status cũ
                    if (Schema::hasColumn('orders', 'status')) {
                        $q->orWhere('status', 'paid');
                    }
                })
                ->exists();
        }

        // --- Fallback: Check theo product_id trong order_details (nếu DB có cột này) ---
        if ($hasOrderDetailsProductId) {
            $hasAnyOrder = $hasAnyOrder || OrderDetail::where('product_id', $product->id)->exists();

            $hasSuccessfulOrder = $hasSuccessfulOrder || OrderDetail::where('product_id', $product->id)
                ->whereHas('order', function ($q) {
                    if (Schema::hasColumn('orders', 'delivery_status')) {
                        $q->orWhere('delivery_status', 'delivered');
                    }
                    if (Schema::hasColumn('orders', 'payment_status')) {
                        $q->orWhere('payment_status', 'paid');
                    }
                    if (Schema::hasColumn('orders', 'status')) {
                        $q->orWhere('status', 'paid');
                    }
                })
                ->exists();
        }

        if ($hasSuccessfulOrder) {
            return back()->with('error', 'Không thể xóa sản phẩm vì sản phẩm đã có đơn hàng ở trạng thái THÀNH CÔNG. Vui lòng ẩn/ngừng bán thay vì xóa để tránh mất dữ liệu.');
        }

        if ($hasAnyOrder) {
            return back()->with('error', 'Không thể xóa sản phẩm vì sản phẩm đã phát sinh trong đơn hàng. Vui lòng ẩn/ngừng bán thay vì xóa để tránh mất dữ liệu.');
        }

        // Xóa sản phẩm (không có phát sinh đơn)
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
    // public function search(Request $request)
    // {
    //     $keyword = $request->keyword;

    //     $products = Product::where('name', 'LIKE', '%' . $keyword . '%')
    //         ->orWhere('description', 'LIKE', '%' . $keyword . '%')
    //         ->paginate(12);

    //     $categories = Category::all();

    //     return view('search.search', compact('products', 'categories', 'keyword'));
    // }



    
}
