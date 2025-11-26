<?php

use Illuminate\Support\Facades\Route;

// CLIENT CONTROLLERS
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Models\Order;


// ADMIN CONTROLLERS
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\DiscountCodeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// FRONTEND
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{id}', [HomeController::class, 'category'])->name('category.show');
Route::get('/product/{id}', [HomeController::class, 'show'])->name('product.show');
Route::get('/search', [ProductController::class, 'search'])->name('search');

// CART
Route::group(['prefix' => 'cart', 'as' => 'cart.'], function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});


// TRANG CHECKOUT (GET)
Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout.index')
    ->middleware('cart_notempty');

// XỬ LÝ CHECKOUT (POST)
Route::post('/checkout', [CheckoutController::class, 'process'])
    ->name('checkout.process')
    ->middleware('cart_notempty');

Route::get('/check-zalopay-status/{order}', function(Order $order) {
    // Nếu bạn tích hợp callback thật thì kiểm tra ở đây
    // Hiện tại chỉ demo: giả sử đã thanh toán nếu > 30 giây
    $paid = $order->updated_at->diffInSeconds(now()) > 30;
    if ($paid && $order->status === 'pending') {
        $order->update(['status' => 'paid']);
    }
    return response()->json(['paid' => $order->status === 'paid']);
})->name('check.zalopay.status');

// AUTH (login / register)
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

// ADMIN
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('orders', OrderController::class)->only(['index', 'show']);

    Route::resource('product-variants', ProductVariantController::class)->names('product_variants');

    Route::resource('attributes', AttributeController::class);
    Route::resource('attribute_values', AttributeValueController::class);

    Route::resource('discount-codes', DiscountCodeController::class);
});
