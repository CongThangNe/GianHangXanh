<?php

namespace App\Helpers;

class OrderStatus
{
    public static function labels(): array
    {
        return [
            'pending'   => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'preparing' => 'Đang chuẩn bị hàng',
            'shipping'  => 'Đang vận chuyển',
            'delivered' => 'Đã giao thành công',
            'cancelled' => 'Đã huỷ',
        ];
    }

    public static function label(string $status): string
    {
        return self::labels()[$status] ?? 'Không xác định';
    }
}
