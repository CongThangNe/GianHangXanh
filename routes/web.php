<?php

use Illuminate\Support\Facades\Route;
//AUTH CONTROLLER
use App\Http\Controllers\AuthController;
// CLIENT CONTROLLERS
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Models\Order;
use App\Http\Controllers\OrderGuestController;



use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\DiscountCodeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Pagination\LengthAwarePaginator;
// ADMIN CONTROLLERS

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
Route::get('/products', [HomeController::class, 'allProducts'])->name('products.all');
Route::view('/intro', 'intro.intro')->name('intro');
// CART
Route::group(['prefix' => 'cart', 'as' => 'cart.'], function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    Route::post('/apply-discount', [CartController::class, 'applyDiscount'])->name('applyDiscount');
    Route::post('/remove-discount', [CartController::class, 'removeDiscount'])->name('removeDiscount');
});

// Không đăng nhập
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Đăng xuất
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// TRANG CHECKOUT (GET)
Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout.index')
    ->middleware('cart_notempty');

// XỬ LÝ CHECKOUT (POST)
Route::post('/checkout', [CheckoutController::class, 'process'])
    ->name('checkout.process')
    ->middleware('cart_notempty');

Route::get('/check-zalopay-status/{order}', function (Order $order) {
    // Nếu bạn tích hợp callback thật thì kiểm tra ở đây
    // Hiện tại chỉ demo: giả sử đã thanh toán nếu > 30 giây
    $paid = $order->updated_at->diffInSeconds(now()) > 30;
    if ($paid && ($order->payment_status ?? 'unpaid') === 'unpaid') {
        $order->update(['payment_status' => 'paid']);
    }
    return response()->json(['paid' => ($order->payment_status ?? 'unpaid') === 'paid']);
})->name('check.zalopay.status');

// AUTH (login / register)
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

// Route tạm để xem giao diện danh sách đơn hàng


// USER PROFILE
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'userIndex'])
        ->name('user.orders.index');

    Route::get('/orders/{order}', [OrderController::class, 'userShow'])
        ->name('user.orders.show');


    // Trang hồ sơ
    Route::get('/profile', [UserProfileController::class, 'show'])
        ->name('profile.show');

    // Cập nhật thông tin hồ sơ
    Route::put('/profile', [UserProfileController::class, 'update'])
        ->name('profile.update');

    // 🔥 Trang đổi mật khẩu (GET)
    Route::get('/profile/change-password', [UserProfileController::class, 'editPassword'])
        ->name('profile.password');

    // 🔥 Xử lý đổi mật khẩu (POST hoặc PUT đều được — tôi dùng POST cho chuẩn)
    Route::post('/profile/change-password', [UserProfileController::class, 'updatePassword'])
        ->name('profile.password.update');
    // Route hủy đơn hàng dành cho khách vãng lai
    Route::delete('/orders/cancel/{order_code}', [OrderGuestController::class, 'cancel'])
        ->name('orders.cancel');
});

// ADMIN
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý tài khoản (mới: role admin/khách hàng/nhân viên)
    Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update']);

    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    // Thêm route cập nhật trạng thái
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.updateStatus');

    Route::resource('product-variants', ProductVariantController::class)->names('product_variants');

    Route::resource('attributes', AttributeController::class);
    Route::resource('attribute_values', AttributeValueController::class);

    Route::resource('discount-codes', DiscountCodeController::class);
});
// TRANG HIỂN THỊ CHECKOUT (GET)
Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout.index')
    ->middleware('cart_notempty');

// XỬ LÝ THANH TOÁN (POST)
Route::post('/checkout', [CheckoutController::class, 'process'])
    ->name('checkout.process')
    ->middleware('cart_notempty');

// Trang thanh toán online
Route::get('/payment/zalopay', [PaymentController::class, 'zaloPayApp'])->name('payment.zalopay');
Route::get('/payment/zalopay/return', [PaymentController::class, 'zaloReturn'])->name('payment.zalopay.return');

//VNPAY
Route::get('/payment/create', [PaymentController::class, 'createPayment'])->name('payment.create');
Route::get('/payment/return', [PaymentController::class, 'vnpayReturn'])->name('payment.return');

// banners
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);
});
