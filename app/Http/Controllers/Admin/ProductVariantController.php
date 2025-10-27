<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;

class ProductVariantController extends Controller
{
    public function index()
    {
        $variants = ProductVariant::with('product')->paginate(20);
        return view('admin.product_variants.index', compact('variants'));
    }

public function create()
{
    $products = Product::all();
    $attributes = Attribute::with('values')->get();
    return view('admin.product_variants.create', compact('products', 'attributes'));
}


    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'sku' => 'required|unique:product_variants',
            'price' => 'required|numeric',
            'stock' => 'required|integer'
        ]);

        ProductVariant::create($request->all());
        return redirect()->route('admin.product_variants.index')->with('success', 'Thêm biến thể thành công');
    }

public function edit($id)
{
    $variant = ProductVariant::findOrFail($id);
    $products = Product::all();
    $attributes = Attribute::with('values')->get();
    $selectedValues = $variant->attributeValues->pluck('id')->toArray();
    return view('admin.product_variants.edit', compact('variant', 'products', 'attributes', 'selectedValues'));
}


    public function update(Request $request, $id)
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->update($request->all());
        return redirect()->route('admin.product_variants.index')->with('success', 'Cập nhật biến thể thành công');
    }

    public function destroy($id)
    {
        ProductVariant::destroy($id);
        return redirect()->route('admin.product_variants.index')->with('success', 'Xóa biến thể thành công');
    }
}
