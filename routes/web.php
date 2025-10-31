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
use App\Http\Controllers\Admin\DiscountCodeController;


// ==================== KHU VỰC QUẢN TRỊ (ADMIN) ====================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // **[ĐÃ KHẮC PHỤC LỖI RouteNotFoundException]**
    // Sử dụng Route::resource tự động tạo ra products.index, products.create, products.edit, products.destroy, v.v.
    Route::resource('products', ProductController::class);

    // Quản lý danh mục
    Route::resource('categories', CategoryController::class);

    // Quản lý đơn hàng
    Route::resource('orders', OrderController::class)->only(['index', 'show']);

    // Giả sử có group admin
    Route::resource('product_variants', App\Http\Controllers\Admin\ProductVariantController::class);
    // Quản lý thuộc tính & giá trị
    Route::resource('attributes', App\Http\Controllers\Admin\AttributeController::class);
    Route::resource('attribute_values', App\Http\Controllers\Admin\AttributeValueController::class);

    // Quản lý mã giảm giá
    Route::resource('discount-codes', DiscountCodeController::class);
});
