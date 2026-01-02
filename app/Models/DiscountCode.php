<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class DiscountCode extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'max_discount_value',
        'max_uses',
        'used_count',
        'starts_at',
        'expires_at',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function isValid(): bool
    {
        if (!$this->active) return false;

        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;

        if ($this->max_uses > 0 && $this->used_count >= $this->max_uses)
            return false;

        return true;
    }
}
