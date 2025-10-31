<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;

    // Tên bảng tương ứng trong database
    protected $table = 'discount_codes'; 

    // Các trường có thể được gán hàng loạt (mass assignable)
    protected $fillable = [
        'code', 
        'discount_percent', // Giảm theo % (Loại 1)
        'discount_value',   // Giảm trực tiếp (Loại 2) - Cần thêm cột này vào DB nếu chưa có
        'used_count',       // Số lần đã sử dụng
        'max_uses',         // Giới hạn số lần sử dụng
        'expires_at',       // Ngày hết hạn
        // Bạn có thể dùng discount_percent và discount_value như sau:
        // - Loại 1: discount_percent có giá trị, discount_value là 0/null
        // - Loại 2: discount_value có giá trị, discount_percent là 0/null
    ];

    // Chuyển đổi kiểu dữ liệu
    protected $casts = [
        'expires_at' => 'datetime',
    ];
}