<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        // Create 20 products distributed across categories
        for ($i=1; $i<=20; $i++) {
            $cat = $categories->random();
            Product::create([
                'name' => $cat->name . ' Sản phẩm ' . $i,
                'description' => 'Mô tả cho ' . $cat->name . ' sản phẩm ' . $i,
                'price' => rand(100000, 5000000),
                'stock' => rand(0,50),
                'image' => null,
                'category_id' => $cat->id,
            ]);
        }
    }
}
