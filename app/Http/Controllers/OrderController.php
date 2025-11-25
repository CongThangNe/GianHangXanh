<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('details.variant.product')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with([
            'details.variant.product',
            'details.variant.values.attribute'
        ])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }
}
