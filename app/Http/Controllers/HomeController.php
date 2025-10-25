<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $products = Product::latest()->paginate(12);
        return view('products.index', compact('categories','products'));
    }

    public function category($id)
    {
        $categories = Category::all();
        $products = Product::where('category_id', $id)->paginate(12);
        return view('products.index', compact('categories','products'));
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('products.show', compact('product'));
    }
}
