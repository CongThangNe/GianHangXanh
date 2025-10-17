<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = ['order_id','product_id','quantity','price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
