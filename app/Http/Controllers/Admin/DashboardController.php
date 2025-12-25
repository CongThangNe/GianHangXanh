<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Thêm thư viện xử lý thời gian

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Xử lý khoảng thời gian (Mặc định từ đầu tháng đến hiện tại)
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::now()->endOfDay();

        // 2. Thống kê theo khoảng thời gian
        $userCount  = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $orderCount = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Doanh thu chỉ tính các đơn đã thanh toán trong khoảng thời gian
        $revenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        // Hàng tồn kho thường là con số hiện tại (không lọc theo thời gian tạo)
        $stockCount = ProductVariant::sum('stock');

        // 3. Danh sách dữ liệu hiển thị (Giữ nguyên latest hoặc lọc nếu muốn)
        $users = User::whereBetween('created_at', [$startDate, $endDate])->latest()->take(5)->get();
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])->latest()->take(5)->get();

        // 4. Top sản phẩm bán chạy (Lọc theo thời gian đơn hàng)
        $topSellingProducts = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->join('product_variants as pv', 'pv.id', '=', 'od.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->where('o.payment_status', 'paid')
            ->whereBetween('o.created_at', [$startDate, $endDate]) // Thêm lọc thời gian ở đây
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
            'orders',
            'startDate',
            'endDate'
        ));
    }
}