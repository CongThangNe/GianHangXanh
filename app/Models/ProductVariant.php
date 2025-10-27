<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'sku', 'price', 'stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function values()
    {
        return $this->hasMany(ProductVariantValue::class, 'variant_id');
    }
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variant_values', 'product_variant_id', 'attribute_value_id');
    }
}
