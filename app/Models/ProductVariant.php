<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock',
        'image',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function values()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_variant_values',
            'product_variant_id',
            'attribute_value_id'
        );
    }

   public function attributeValues()
{
    return $this->belongsToMany(
        \App\Models\AttributeValue::class,
        'product_variant_values', 
        'product_variant_id',     
        'attribute_value_id'     
    );
}

}
