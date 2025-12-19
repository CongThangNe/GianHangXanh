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
        $userCount  = User::count();
        $orderCount = Order::count();
        $revenue = Order::where('status', 'paid')->sum('total');
        $stockCount = ProductVariant::sum('stock');

        // Danh sách user mới
        $users = User::latest()->take(5)->get();

        
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
