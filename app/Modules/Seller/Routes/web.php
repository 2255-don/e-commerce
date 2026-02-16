<?php

use Illuminate\Support\Facades\Route;
use Modules\Seller\Controllers\Web\SellerDashboardController;
use Modules\Seller\Controllers\Web\SellerProductController;
use Modules\Seller\Controllers\Web\SellerOrderController;
use Modules\Seller\Controllers\Web\SellerLicenseController;

/*
|--------------------------------------------------------------------------
| Seller Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('seller')->name('seller.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
    
    // Products Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [SellerProductController::class, 'index'])->name('index');
        Route::get('/create', [SellerProductController::class, 'create'])->name('create');
        Route::post('/', [SellerProductController::class, 'store'])->name('store');
        Route::get('/{id}', [SellerProductController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SellerProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SellerProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [SellerProductController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/stock', [SellerProductController::class, 'updateStock'])->name('update-stock');
        Route::post('/{id}/deactivate', [SellerProductController::class, 'deactivate'])->name('deactivate');
    });
    
    
    // Orders
    Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [SellerOrderController::class, 'show'])->name('orders.show');
    
    // License Management
    Route::get('/license', [SellerLicenseController::class, 'show'])->name('license');
    Route::post('/register', [SellerLicenseController::class, 'store'])->name('register');
    Route::post('/license/renew', [SellerLicenseController::class, 'renew'])->name('license.renew');
    Route::post('/license/pay', [SellerLicenseController::class, 'payWithWallet'])->name('license.wallet');

});
