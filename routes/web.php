<?php

use Illuminate\Support\Facades\Route;

// ==================== CLIENT CONTROLLERS ====================
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;

// ==================== ADMIN CONTROLLERS ====================
// GIẢ ĐỊNH TẤT CẢ CONTROLLER ADMIN NẰM TRONG NAMESPACE 'Admin\'
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeValueController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| File định nghĩa route cho website.
*/

// ==================== GIAO DIỆN NGƯỜI DÙNG (CLIENT) ====================

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{id}', [HomeController::class, 'category'])->name('category.show');
Route::get('/product/{id}', [HomeController::class, 'show'])->name('product.show');

// Giỏ hàng
Route::group(['prefix' => 'cart', 'as' => 'cart.'], function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
});


// ==================== KHU VỰC QUẢN TRỊ (ADMIN) ====================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // **[ĐÃ KHẮC PHỤC LỖI RouteNotFoundException]**
    // Sử dụng Route::resource tự động tạo ra products.index, products.create, products.edit, products.destroy, v.v.
    Route::resource('products', ProductController::class);

    // Quản lý danh mục
    Route::resource('categories', CategoryController::class);

    // Quản lý đơn hàng
    Route::resource('orders', OrderController::class)->except(['create', 'store']); // Chỉ cho phép xem, sửa, xóa

    // Quản lý các thuộc tính liên quan
    Route::resource('product-variants', ProductVariantController::class)->names('product_variants');
    Route::resource('attributes', AttributeController::class);
    Route::resource('attribute_values', AttributeValueController::class);
});
