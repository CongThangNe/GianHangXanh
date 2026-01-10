<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'total',
        'payment_method',
        'payment_status',
        'delivery_status',
        'status',
        'customer_name',
        'customer_phone',
        'customer_address',
         'customer_email', 
        'note',
    ];

    protected $casts = [
        'total' => 'integer',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}

