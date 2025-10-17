<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = ['Điện thoại','Laptop','Đồng hồ','Giày','Áo quần'];
        foreach ($cats as $c) {
            Category::create(['name'=>$c,'description'=>$c.' mô tả']);
        }
    }
}
