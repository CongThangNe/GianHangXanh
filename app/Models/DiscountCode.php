<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class DiscountCode extends Model
{
    use HasFactory;

    protected $table = 'discount_codes';

    protected $fillable = [
        'code',
        'type',
        'discount_percent',
        'discount_value',
        'max_discount_value',
        'max_uses',
        'used_count',
        'starts_at',
        'expires_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Tự động chuyển string -> Carbon khi đọc từ DB
    public function getStartsAtAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getExpiresAtAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }
}
