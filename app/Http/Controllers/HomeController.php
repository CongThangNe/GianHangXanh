<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
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
            'featuredProducts'
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
    public function allProducts()
    {
        $categories = Category::all();
        $products = Product::paginate(12);

        return view('products.index', compact('categories', 'products'));
    }
}
