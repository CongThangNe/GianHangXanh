<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        // temporary: show empty or demo data until cart implemented
        return view('cart.index');
    }
}
