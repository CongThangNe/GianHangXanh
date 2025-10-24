<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\ProductVariantController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/category/{id}', [HomeController::class, 'category']);
Route::get('/product/{id}', [HomeController::class, 'show']);
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

Route::prefix('admin')->name('admin.')->group(function () {
    // Quản lý sản phẩm
    Route::resource('products', ProductController::class);

    // Quản lý danh mục (CRUD đầy đủ trừ show)
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Quản lý đơn hàng
    Route::resource('orders', OrderController::class)->only(['index', 'show']);

    // Giả sử có group admin
    Route::get('/product_variants', [ProductVariantController::class, 'index'])->name('product_variants.index');
});
