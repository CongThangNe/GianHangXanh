<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_code', 'total', 'payment_method', 'status',
        'customer_name', 'customer_phone', 'customer_address', 'note'
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
