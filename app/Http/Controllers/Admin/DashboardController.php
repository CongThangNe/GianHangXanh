<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Nếu là AJAX request → chỉ trả về phần nội dung
        // if ($request->ajax()) {
        //     return view('admin.dashboard');
        // }

        // Nếu là truy cập trực tiếp → load trong layout admin
        // return view('layouts.admin', [
        //     'content' => view('admin.dashboard')->render()
        // ]);

        // GET list top 3 best seller products
        // $topSellingProducts = Product::withCount(['orderItems as total_sold' => function ($query) {
        //     $query->select(\DB::raw("SUM(quantity)"));
        // }])->orderByDesc('total_sold')->take(3)->get();

        // return view('admin.dashboard', compact('users', 'categories', 'products' , 'orders' , '$topSellingProducts'));

        // Tổng quan
        $userCount  = User::count();
        $orderCount = Order::count();
        $revenue = Order::where('status', 'paid')->sum('total');
        $stockCount = ProductVariant::sum('stock');

        // Danh sách user mới
        $users = User::latest()->take(5)->get();

        // 🔥 TOP 3 SẢN PHẨM BÁN CHẠY
       $topSellingProducts = Product::withSum(
            ['orderDetails as total_sold' => function ($query) {
                    $query->whereHas('order', function ($q) {
                        $q->where('status', 'paid');
                    });
                }], 
                'quantity'
            )
            ->orderByDesc('total_sold')
            ->take(3)
            ->get();
            // dd(
            //     Product::first()->orderDetails()->get()
            // );
        return view('admin.dashboard', compact(
            'userCount',
            'orderCount',
            'revenue',
            'stockCount',
            'users',
            'topSellingProducts'
        ));
    }
}
