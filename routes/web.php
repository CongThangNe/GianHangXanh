<?php

use Illuminate\Support\Facades\Route;

// ==================== CLIENT CONTROLLERS ====================
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;

// ==================== ADMIN CONTROLLERS ====================
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| File định nghĩa route cho website.
|--------------------------------------------------------------------------
*/

// ==================== GIAO DIỆN NGƯỜI DÙNG (CLIENT) ====================

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Danh mục sản phẩm
Route::get('/category/{id}', [HomeController::class, 'category'])->name('category.show');

// Chi tiết sản phẩm
Route::get('/product/{id}', [HomeController::class, 'show'])->name('product.show');

// -------------------- GIỎ HÀNG --------------------
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');      // Trang giỏ hàng
    Route::post('/add', [CartController::class, 'add'])->name('add');      // Thêm vào giỏ
    Route::post('/update', [CartController::class, 'update'])->name('update');  // Cập nhật SL
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');  // Xóa sản phẩm
});

// ==================== KHU VỰC QUẢN TRỊ (ADMIN) ====================

Route::prefix('admin')->name('admin.')->group(function () {
    // Trang tổng quan
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý sản phẩm
    Route::resource('products', ProductController::class);

    // Quản lý danh mục
    Route::resource('categories', CategoryController::class);

    // Quản lý đơn hàng
    Route::resource('orders', OrderController::class)->except(['create', 'store']); // Chỉ cho phép xem, sửa, xóa

    // Quản lý biến thể, thuộc tính, giá trị thuộc tính
    Route::resource('product-variants', ProductVariantController::class)->names('product_variants');
    Route::resource('attributes', AttributeController::class);
    Route::resource('attribute-values', AttributeValueController::class)->names('attribute_values');
});

