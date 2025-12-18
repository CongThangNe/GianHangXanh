<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;

class HomeController extends Controller
{
    /**
     * Trang chủ:
     * - Top 10 sản phẩm nổi bật
     * - Sản phẩm liên quan theo danh mục (mỗi danh mục lấy 8 sp mới nhất)
     * - Banner (active)
     * - Hỗ trợ tìm kiếm nhanh bằng keyword (nếu có)
     */
    public function index(Request $request)
    {
        $categories = Category::all();

        // Banner
        $banners = Banner::where('status', 1)
            ->orderBy('sort_order')
            ->get();

        $keyword = $request->input('keyword');

        if ($keyword) {
            // Nếu đang tìm kiếm thì trả kết quả phân trang
            $products = Product::where('name', 'like', '%' . $keyword . '%')
                ->orWhere('description', 'like', '%' . $keyword . '%')
                ->paginate(12);

            return view('products.index', compact('categories', 'products', 'keyword', 'banners'));
        }

        // ==========================
        // TOP 10 SẢN PHẨM NỔI BẬT
        // (vì chưa có trường sold/rating nên ưu tiên "mới nhất")
        // ==========================
        $products = Product::latest()->take(10)->get();

        // ==========================
        // SẢN PHẨM LIÊN QUAN THEO DANH MỤC
        // (mỗi danh mục lấy 8 sp mới nhất)
        // ==========================
        $productsByCategory = $categories->map(function ($category) {
            $category->setRelation('products', Product::where('category_id', $category->id)
                ->latest()
                ->take(8)
                ->get());

            return $category;
        });

        return view('products.index', compact(
            'categories',
            'products',
            'productsByCategory',
            'keyword',
            'banners'
        ));
    }

    /**
     * Trang danh mục
     */
    public function category($id)
    {
        $categories = Category::all();
        $products = Product::with('variants')
            ->where('category_id', $id)
            ->paginate(12);

        return view('products.index', compact('categories', 'products'));
    }

    /**
     * Trang chi tiết sản phẩm + sản phẩm liên quan cùng danh mục
     */
    public function show($id)
    {
        $product = Product::with('category', 'variants')->findOrFail($id);

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(8)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Xem tất cả sản phẩm (route: /products)
     */
    public function allProducts(Request $request)
    {
        $categories = Category::all();
        $products = Product::latest()->paginate(12);
        $keyword = null;

        // dùng chung view search để hiển thị danh sách sản phẩm
        return view('search.search', compact('categories', 'products', 'keyword'));
    }
}
