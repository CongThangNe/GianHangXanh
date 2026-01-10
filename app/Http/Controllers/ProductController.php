<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;


class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->paginate(10);
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


// public function show($id)
// {
//     $product = Product::findOrFail($id);
//     $categories = Category::all();
//     return view('products.show', compact('product','categories'));
// }

    public function show(Request $request, $id)
{
    // 1. Sản phẩm đang xem
    $product = Product::with('category', 'variants.values')
        ->findOrFail($id);

    // 2. Query sản phẩm liên quan
    $query = Product::where('id', '!=', $product->id)
        ->where('category_id', $product->category_id);

    // 3. Lọc theo giá
    if ($request->filled('price_min')) {
        $query->where('price', '>=', $request->price_min);
    }

    if ($request->filled('price_max')) {
        $query->where('price', '<=', $request->price_max);
    }

    // 4. Lọc theo attributes
    if ($request->filled('attributes')) {
        foreach ($request->attributes as $attributeId => $valueId) {
            if ($valueId) {
                $query->whereHas('variants.values', function ($q) use ($valueId) {
                    $q->where('attribute_value_id', $valueId);
                });
            }
        }
    }

    // 5. Lấy sản phẩm liên quan
    $relatedProducts = $query->limit(8)->get();

    //  6. 
    $attributes = Attribute::with('values')->get();

    // 7. Trả về view
    return view('products.show', compact(
        'product',
        'relatedProducts',
        'attributes'
    ));
}

}
