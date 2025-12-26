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

    /**
     * Nhãn hiển thị biến thể (vd: "500g / Đỏ").
     * Dựa trên các attribute values gắn với variant.
     */
    protected $appends = ['variant_label'];

    public function getVariantLabelAttribute(): string
    {
        // values có thể đã được eager load ở controller
        $label = $this->values?->pluck('value')->filter()->implode(' / ');
        return $label ?: 'Mặc định';
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Quan hệ dùng để load value → attribute
    public function values()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_variant_values',
            'product_variant_id',
            'attribute_value_id'
        );
    }

    // Quan hệ alias để dùng trong các chỗ khác (nếu cần)
    public function attributeValues()
    {
        return $this->values();
    }
}
