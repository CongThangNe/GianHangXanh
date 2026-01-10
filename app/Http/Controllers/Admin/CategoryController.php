<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Schema;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name'=>'required']);
        Category::create($request->only('name','description'));
        return redirect()->route('admin.categories.index')->with('success','Thêm danh mục thành công');
    }

    // HÀM SỬA
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    //  HÀM CẬP NHẬT
    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category = Category::findOrFail($id);
        $category->update($request->only('name','description'));
        return redirect()->route('admin.categories.index')->with('success','Cập nhật danh mục thành công');
    }

    // 🟢 HÀM XÓA
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // =====================
        // VALIDATE KHÔNG CHO XÓA DANH MỤC
        // - Nếu danh mục có sản phẩm đã phát sinh trong đơn hàng (đặc biệt đơn thành công) => CHẶN XÓA
        // - Tránh mất dữ liệu vì order_details liên kết với product/variant
        // =====================

        // 1) Nếu danh mục đã có sản phẩm => chặn xóa (tránh mất dữ liệu, ảnh hưởng FK, lịch sử)
        $productIds = Product::where('category_id', $category->id)->pluck('id');

        if ($productIds->isNotEmpty()) {
            return back()->with('error', 'Không thể xóa danh mục vì danh mục đang chứa sản phẩm.');
        }

        // (Danh mục rỗng => cho phép xóa)

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success','Đã xóa danh mục');
    }
}
