<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        // Banner
        $banners = Banner::where('status', 1)
            ->orderBy('sort_order')
            ->get();
        $keyword = $request->input('keyword');

        // Thêm dòng FEATURED PRODUCTS
        $featuredProducts = Product::latest()->take(4)->get();

        if ($keyword) {
            $products = Product::where('name', 'like', '%' . $keyword . '%')->paginate(12);
        } else {
            $products = Product::latest()->take(4)->get();
        }

        return view('products.index', compact(
            'categories',
            'products',
            'keyword',
            'featuredProducts',
            'banners'
        ));
    }

    public function category($id)
    {
        $categories = Category::all();
        $products = Product::with('variants')->where('category_id', $id)->paginate(12);
        return view('products.index', compact('categories', 'products'));
    }

    public function show($id)
    {
        $product = Product::with('category', 'variants')->findOrFail($id);
        return view('products.show', compact('product'));
    }

    
}
