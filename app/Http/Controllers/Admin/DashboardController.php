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
        $revenue = Order::where('payment_status', 'paid')->sum('total');
        $stockCount = ProductVariant::sum('stock');

        // Danh sách user mới
        $users = User::latest()->take(3)->get();

        // Đơn hàng mới
        $orders = Order::latest()->take(3)->get();

        //sản phẩm bán chạy
        $topSellingProducts = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->join('product_variants as pv', 'pv.id', '=', 'od.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->where('o.payment_status', 'paid') // chỉ tính đơn đã thanh toán
            ->select(
                'p.id',
                'p.name',
                'p.price',
                'p.image',
                DB::raw('SUM(od.quantity) as total_sold')
            )
            ->groupBy('p.id', 'p.name', 'p.price', 'p.image')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();
        return view('admin.dashboard', compact(
            'userCount',
            'orderCount',
            'revenue',
            'stockCount',
            'users',
            'topSellingProducts',
            'orders'
        ));

    }
}
