<?php

use Illuminate\Support\Facades\Route;
//AUTH CONTROLLER
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
// CLIENT CONTROLLERS
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SupportController;
use App\Models\Order;
use App\Http\Controllers\OrderGuestController;



use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\DiscountCodeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Frontend\NewsController as FrontNewsController;
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
// Support / Contact
Route::get('/support', [SupportController::class, 'index'])->name('support.index');
Route::post('/support', [SupportController::class, 'store'])->name('support.store');

// CART (Require login for viewing/using cart)
Route::group(['prefix' => 'cart', 'as' => 'cart.', 'middleware' => ['cart_auth']], function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/update-variant', [CartController::class, 'updateVariant'])->name('updateVariant');
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

    // QUÊN MẬT KHẨU (gửi link reset qua email)
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

    // ĐẶT LẠI MẬT KHẨU (từ link email)
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Đăng xuất
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// TRANG CHECKOUT (GET)
Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout.index')
    ->middleware(['cart_notempty']);

// XỬ LÝ CHECKOUT (POST)
Route::post('/checkout', [CheckoutController::class, 'process'])
    ->name('checkout.process')
    ->middleware(['cart_notempty']);

Route::get('/check-zalopay-status/{order}', function (Order $order) {
    // Nếu bạn tích hợp callback thật thì kiểm tra ở đây
    // Hiện tại chỉ demo: giả sử đã thanh toán nếu > 30 giây
    $paid = $order->updated_at->diffInSeconds(now()) > 30;
    if ($paid && ($order->payment_status ?? 'unpaid') === 'unpaid') {
        $order->update(['payment_status' => 'paid']);
    }
    return response()->json(['paid' => ($order->payment_status ?? 'unpaid') === 'paid']);
})->name('check.zalopay.status');

// (ĐÃ CÓ ROUTE LOGIN/REGISTER BẰNG CONTROLLER Ở TRÊN)

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
// - Customer: /admin -> 404
// - Staff: vào được /admin nhưng bị chặn /admin/users và không thấy Doanh thu
// - Admin: full quyền
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin_access'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý tài khoản (mới: role admin/khách hàng/nhân viên)
    // Admin có thể xem, sửa vai trò và xóa tài khoản.
    Route::middleware('admin_only')->group(function () {
        Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update', 'destroy']);
    });

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
// Trang thanh toán online (khách vãng lai vẫn có thể thanh toán)
// VNPAY
// VNPAY
// VNPAY
Route::get('/payment/create', [PaymentController::class, 'createPayment'])
    ->name('payment.create');

Route::get('/payment/return', [PaymentController::class, 'vnpayReturn'])
    ->name('payment.return');

Route::get('/payment/ipn', [PaymentController::class, 'vnpayIpn'])
    ->name('payment.ipn');


// banners
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin_access'])->group(function () {
    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);
});
// search nâng cao trong ctsp
// Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin_access'])->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);
});


// tin tức
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin_access'])
    ->group(function () {
        Route::resource('news', NewsController::class);
    });

Route::get('/tin-tuc', [NewsController::class, 'index'])
    ->name('news.index');

// FRONTEND NEWS

Route::get('/tin-tuc', [FrontNewsController::class, 'index'])
    ->name('news.index');

Route::get('/tin-tuc/{id}', [FrontNewsController::class, 'show'])
    ->name('news.show');

