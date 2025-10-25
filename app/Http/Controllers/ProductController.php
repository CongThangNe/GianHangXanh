<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

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
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'price'=>'required|numeric',
            'category_id'=>'required|exists:categories,id'
        ]);

        $data = $request->only(['name','description','price','stock','category_id']);
        if($request->hasFile('image')){
            $path = $request->file('image')->store('products','public');
            $data['image'] = basename($path);
        }
        Product::create($data);
        return redirect()->route('admin.products.index')->with('success','Thêm sản phẩm thành công');
    }
    public function edit($id) {
    $product = Product::findOrFail($id);
    $categories = Category::all();
    return view('admin.products.edit', compact('product','categories'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required',
        'price' => 'required|numeric',
        'category_id' => 'required|exists:categories,id'
    ]);

    $product = Product::findOrFail($id);

    $data = $request->only(['name','description','price','stock','category_id']);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('products', 'public');
        $data['image'] = basename($path);
    }

    $product->update($data);

    return redirect()->route('admin.products.index')->with('success', 'Cập nhật thành công');
}


public function destroy($id) {
    Product::findOrFail($id)->delete();
    return back()->with('success','Đã xóa sản phẩm');
}


}
