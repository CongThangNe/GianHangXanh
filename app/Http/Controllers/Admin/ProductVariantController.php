<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        $variants = $product->variants()->with('attributeValues.attribute')->get();
        return view('admin.products.variants.index', compact('product', 'variants'));
    }

    public function create(Product $product)
    {
        $attributes = Attribute::with('values')->get();
        return view('admin.products.variants.create', compact('product', 'attributes'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'sku' => 'nullable|string|unique:product_variants',
            'attribute_values' => 'required|array',
            'attribute_values.*' => 'exists:attribute_values,id',
        ]);

        $variant = $product->variants()->create([
            'sku' => $request->sku,
            'price' => $request->price,
            'quantity' => $request->quantity,
        ]);

        $variant->attributeValues()->attach($request->attribute_values);

        return redirect()->route('admin.products.variants.index', $product)->with('success', 'Variant created successfully.');
    }

    public function edit(Product $product, ProductVariant $variant)
    {
        $attributes = Attribute::with('values')->get();
        $selectedValues = $variant->attributeValues->pluck('id')->toArray();
        return view('admin.product_variants.edit', compact('product', 'variant', 'attributes', 'selectedValues'));
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        $request->validate([
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'sku' => 'nullable|string|unique:product_variants,sku,' . $variant->id,
            'attribute_values' => 'required|array',
            'attribute_values.*' => 'exists:attribute_values,id',
        ]);

        $variant->update([
            'sku' => $request->sku,
            'price' => $request->price,
            'quantity' => $request->quantity,
        ]);

        $variant->attributeValues()->sync($request->attribute_values);

        return redirect()->route('admin.products.variants.index', $product)->with('success', 'Variant updated successfully.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        $variant->delete();
        return redirect()->route('admin.products.variants.index', $product)->with('success', 'Variant deleted successfully.');
    }
}