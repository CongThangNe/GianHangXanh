<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================
        // USERS
        // ============================
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@test.com',
                'password' => Hash::make('admin123'),
                'is_admin' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Customer',
                'email' => 'customer@test.com',
                'password' => Hash::make('123456'),
                'is_admin' => 0,
                'created_at' => now(),
            ],
        ]);

        // ============================
        // CATEGORIES
        // ============================
        $categories = ['Điện thoại', 'Laptop', 'Phụ kiện', 'Thời trang', 'Đồ gia dụng'];
        foreach ($categories as $c) {
            DB::table('categories')->insert([
                'name' => $c,
                'created_at' => now()
            ]);
        }

        // ============================
        // ATTRIBUTES
        // ============================
        $attrColor = DB::table('attributes')->insertGetId(['name' => 'Màu sắc', 'created_at' => now()]);
        $attrStorage = DB::table('attributes')->insertGetId(['name' => 'Dung lượng', 'created_at' => now()]);

        // ============================
        // ATTRIBUTE VALUES
        // ============================
        $colorValues = ['Đỏ', 'Xanh', 'Đen', 'Trắng'];
        $storageValues = ['64GB', '128GB', '256GB'];

        $colorIds = [];
        foreach ($colorValues as $v) {
            $id = DB::table('attribute_values')->insertGetId([
                'attribute_id' => $attrColor,
                'value' => $v,
                'created_at' => now(),
            ]);
            $colorIds[] = $id;
        }

        $storageIds = [];
        foreach ($storageValues as $v) {
            $id = DB::table('attribute_values')->insertGetId([
                'attribute_id' => $attrStorage,
                'value' => $v,
                'created_at' => now(),
            ]);
            $storageIds[] = $id;
        }

        // ============================
        // PRODUCTS + VARIANTS
        // ============================

        for ($i = 1; $i <= 50; $i++) {

            $productId = DB::table('products')->insertGetId([
                'category_id' => rand(1, 5),
                'name' => 'Sản phẩm ' . $i,
                'description' => 'Mô tả sản phẩm demo ' . $i,
                'price' => rand(100000, 15000000),
                'image' => 'https://picsum.photos/seed/p' . $i . '/600/600',
                'created_at' => now(),
            ]);

            // mỗi sản phẩm có 2–4 biến thể
            $variantCount = rand(2, 4);

            for ($v = 1; $v <= $variantCount; $v++) {

                $variantId = DB::table('product_variants')->insertGetId([
                    'product_id' => $productId,
                    'sku' => strtoupper(Str::random(8)),
                    'price' => rand(100000, 15000000),
                    'stock' => rand(5, 40),
                    'image' => 'https://picsum.photos/seed/v' . $productId . $v . '/300/300',
                    'created_at' => now(),
                ]);

                // random màu + dung lượng
                $pickedColor = $colorIds[array_rand($colorIds)];
                $pickedStorage = $storageIds[array_rand($storageIds)];

                DB::table('product_variant_values')->insert([
                    [
                        'product_variant_id' => $variantId,
                        'attribute_value_id' => $pickedColor,
                    ],
                    [
                        'product_variant_id' => $variantId,
                        'attribute_value_id' => $pickedStorage,
                    ]
                ]);
            }
        }

        // ============================
        // DISCOUNT CODES
        // ============================

       DB::table('discount_codes')->insert([
    [
        'code' => 'SALE10',
        'type' => 'percent',
        'discount_percent' => 10,
        'discount_value' => 0,
        'max_discount_value' => 500000,
        'max_uses' => 0,
        'used_count' => 0,
        'starts_at' => now(),
        'expires_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'code' => 'GIAM500K',
        'type' => 'value',
        'discount_percent' => 0,
        'discount_value' => 500000,   // giá trị giảm cố định
        'max_discount_value' => 0,
        'max_uses' => 0,
        'used_count' => 0,
        'starts_at' => now(),
        'expires_at' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ],
]);

    }
}
