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
    public function getImageUrlAttribute()
    {
        if (!$this->image) return null;

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        $path = Str::contains($this->image, '/')
            ? $this->image
            : ('uploads/products/' . $this->image);

        return Storage::url($path);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
