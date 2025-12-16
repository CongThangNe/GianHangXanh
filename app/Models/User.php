<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_STAFF = 'staff';

    /**
     * Danh sách role đang dùng trong hệ thống (chưa cần phân quyền chi tiết).
     */
    public static function roles(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_CUSTOMER => 'Khách hàng',
            self::ROLE_STAFF => 'Nhân viên',
        ];
    }

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'address', 'avatar_path', 'role'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getRoleLabelAttribute(): string
    {
        return self::roles()[$this->role] ?? $this->role;
    }
}
