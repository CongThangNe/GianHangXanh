<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','total_price','status'];

    public function items()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
