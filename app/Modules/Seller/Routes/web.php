<?php

use Illuminate\Support\Facades\Route;
use Modules\Seller\Controllers\Web\SellerDashboardController;
use Modules\Seller\Controllers\Web\SellerProductController;
use Modules\Seller\Controllers\Web\SellerOrderController;

/*
|--------------------------------------------------------------------------
| Seller Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'seller'])->prefix('seller')->name('seller.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
    
    // Products Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [SellerProductController::class, 'index'])->name('index');
        Route::get('/create', [SellerProductController::class, 'create'])->name('create');
        Route::post('/', [SellerProductController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SellerProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SellerProductController::class, 'update'])->name('update');
        Route::post('/{id}/stock', [SellerProductController::class, 'updateStock'])->name('update-stock');
        Route::post('/{id}/deactivate', [SellerProductController::class, 'deactivate'])->name('deactivate');
    });
    
    // Orders
    Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [SellerOrderController::class, 'show'])->name('orders.show');
    
});
