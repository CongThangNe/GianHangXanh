<?php
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    // Products
    Route::get('/products', [ProductController::class, 'apiIndex']);
    Route::get('/products/{id}', [ProductController::class, 'apiShow']);
    Route::post('/products', [ProductController::class, 'apiStore']);
    Route::put('/products/{id}', [ProductController::class, 'apiUpdate']);
    Route::delete('/products/{id}', [ProductController::class, 'apiDelete']);

    // Product Variants
    Route::get('/variants', [ProductVariantController::class, 'apiIndex']);
    Route::get('/variants/{id}', [ProductVariantController::class, 'apiShow']);
    Route::post('/variants', [ProductVariantController::class, 'apiStore']);
    Route::put('/variants/{id}', [ProductVariantController::class, 'apiUpdate']);
    Route::delete('/variants/{id}', [ProductVariantController::class, 'apiDelete']);
});
