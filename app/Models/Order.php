<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'discount_code_id',
        'total_price',
        'status',
        'payment_method'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function items()
    {
        // Alias cho details() để tương thích với chỗ gọi with('items')
        return $this->hasMany(OrderDetail::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
