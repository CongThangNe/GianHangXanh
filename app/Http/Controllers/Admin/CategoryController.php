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

        $productIds = Product::where('category_id', $category->id)->pluck('id');
        $hasOrderDetailsProductId = Schema::hasColumn('order_details', 'product_id');

        $hasAnyOrder = false;
        $hasSuccessfulOrder = false;

        if ($productIds->isNotEmpty()) {
            // Check theo product_id trong order_details (nếu DB có)
            if ($hasOrderDetailsProductId) {
                $hasAnyOrder = OrderDetail::whereIn('product_id', $productIds)->exists();

                $hasSuccessfulOrder = OrderDetail::whereIn('product_id', $productIds)
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

            // Check theo product_variant_id (fallback)
            $variantIds = ProductVariant::whereIn('product_id', $productIds)->pluck('id');
            if ($variantIds->isNotEmpty()) {
                $hasAnyOrder = $hasAnyOrder || OrderDetail::whereIn('product_variant_id', $variantIds)->exists();

                $hasSuccessfulOrder = $hasSuccessfulOrder || OrderDetail::whereIn('product_variant_id', $variantIds)
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
        }

        if ($hasSuccessfulOrder) {
            return back()->with('error', 'Không thể xóa danh mục vì có sản phẩm trong danh mục đã có đơn hàng ở trạng thái THÀNH CÔNG. Vui lòng ẩn/ngừng bán sản phẩm thay vì xóa danh mục.');
        }

        if ($hasAnyOrder) {
            return back()->with('error', 'Không thể xóa danh mục vì có sản phẩm trong danh mục đã phát sinh trong đơn hàng. Vui lòng ẩn/ngừng bán sản phẩm thay vì xóa danh mục.');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success','Đã xóa danh mục');
    }
}
