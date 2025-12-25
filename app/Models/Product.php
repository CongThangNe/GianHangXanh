<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'stock', 'image', 'category_id'];

    protected $appends = ['image_url'];

    /**
     * Trả về URL hiển thị ảnh sản phẩm:
     * - Nếu image là URL (http/https) => trả thẳng
     * - Nếu image là path (có dấu /) => Storage::url(path)
     * - Nếu image là filename => mặc định nằm trong uploads/products/
     */
    // public function getImageUrlAttribute()
    // {
    //     if (!$this->image) return null;

    //     if (Str::startsWith($this->image, ['http://', 'https://'])) {
    //         return $this->image;
    //     }

    //     $path = Str::contains($this->image, '/')
    //         ? $this->image
    //         : ('uploads/products/' . $this->image);

    //     return Storage::url($path);
    // }
    public function getImageUrlAttribute()
{
    if (!$this->image) {
        return 'https://via.placeholder.com/300x300?text=No+Image';
    }

    // Nếu đã là URL
    if (Str::startsWith($this->image, ['http://', 'https://'])) {
        return $this->image;
    }

    // Nếu đã có uploads/products/
    if (Str::contains($this->image, 'uploads/')) {
        return asset('storage/' . $this->image);
    }

    // Nếu chỉ là filename
    return asset('storage/uploads/products/' . $this->image);
}


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Quan hệ bắc cầu để tính lượt bán từ chi tiết đơn hàng
    public function orderDetails()
    {
        // return $this->hasManyThrough(OrderDetail::class, ProductVariant::class);
        return $this->hasManyThrough(
            OrderDetail::class,
            ProductVariant::class,
            'product_id',           // foreign key trên product_variants
            'product_variant_id',   // foreign key trên order_details
            'id',                   // local key trên products
            'id'                    // local key trên product_variants
        );
    }
    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_attribute_values'
        );
    }

    // public function getImageUrlAttribute()
    // {
    //     if (!$this->image) {
    //         return 'https://via.placeholder.com/300x300?text=No+Image';
    //     }

    //     return asset('storage/' . $this->image);
    // }
}
