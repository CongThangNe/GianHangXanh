<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm
     */
    public function index()
    {
        $products = Product::with('category')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Form thêm sản phẩm mới
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Lưu sản phẩm mới vào CSDL
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['name', 'description', 'price', 'stock', 'category_id']);

        // Xử lý upload ảnh (nếu có)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = basename($path);
        }

        Product::create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Thêm sản phẩm thành công!');
    }

    /**
     * Form chỉnh sửa sản phẩm
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $data = $request->only(['name', 'description', 'price', 'stock', 'category_id']);

        // Xử lý upload ảnh (nếu có)
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ (nếu có)
            if (!empty($product->image) && Storage::disk('public')->exists('products/' . $product->image)) {
                Storage::disk('public')->delete('products/' . $product->image);
            }

            $path = $request->file('image')->store('products', 'public');
            $data['image'] = basename($path);
        }

        $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    /**
     * Xóa sản phẩm
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Xóa ảnh trong storage nếu có
        if (!empty($product->image) && Storage::disk('public')->exists('products/' . $product->image)) {
            Storage::disk('public')->delete('products/' . $product->image);
        }

        $product->delete();

        return back()->with('success', 'Đã xóa sản phẩm thành công!');
    }
}
