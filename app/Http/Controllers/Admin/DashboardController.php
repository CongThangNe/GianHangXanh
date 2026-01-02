<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $userCount  = User::count();
        $orderCount = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        $revenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        // Staff không được xem doanh thu
        if (($request->user()->role ?? null) === 'staff') {
            $revenue = 0;
        }

        $stockCount = ProductVariant::sum('stock');

        // Staff cũng không xem danh sách tài khoản trên dashboard
        $users = (($request->user()->role ?? null) === 'staff')
            ? collect()
            : User::whereBetween('created_at', [$startDate, $endDate])->latest()->take(5)->get();
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])->latest()->take(3)->get();

        $topSellingProducts = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->join('product_variants as pv', 'pv.id', '=', 'od.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->where('o.payment_status', 'paid')
            ->whereBetween('o.created_at', [$startDate, $endDate])
            ->select(
                'p.id',
                'p.name',
                'p.price',
                'p.image',
                DB::raw('SUM(od.quantity) as total_sold')
            )
            ->groupBy('p.id', 'p.name', 'p.price', 'p.image')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Thống kê doanh thu theo biểu đồ
        $monthlyData = Order::where('payment_status', 'paid')
            ->select(
                DB::raw('SUM(total) as revenue'),
                DB::raw("DATE_FORMAT(created_at, '%m/%Y') as month_year"),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month')
            )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month', 'month_year')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Chuẩn bị mảng labels (tháng) và data (tiền) để gửi sang View
        $chartLabels = $monthlyData->pluck('month_year')->toArray();
        $chartData = $monthlyData->pluck('revenue')->toArray();

        return view('admin.dashboard', compact(
            'userCount',
            'orderCount',
            'revenue',
            'stockCount',
            'users',
            'topSellingProducts',
            'orders',
            'startDate',
            'endDate',
            'chartLabels', 
            'chartData'    
        ));
    }
}
