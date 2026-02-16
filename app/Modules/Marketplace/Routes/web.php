<?php

use Illuminate\Support\Facades\Route;
use Modules\Marketplace\Controllers\Web\MarketplaceController;
use Modules\Marketplace\Controllers\Web\CartController;
use Modules\Marketplace\Controllers\Web\CheckoutController;
use Modules\Marketplace\Controllers\Web\OrderController;

/*
|--------------------------------------------------------------------------
| Marketplace Web Routes
|--------------------------------------------------------------------------
*/

// Auth-required routes (Marketplace + Cart + Orders)
Route::middleware(['auth'])->group(function () {
    
    // Marketplace routes (authentication required)
    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
    Route::get('/products/{slug}', [MarketplaceController::class, 'show'])->name('marketplace.show');
    Route::get('/marketplace/search', [MarketplaceController::class, 'search'])->name('marketplace.search');
    
    // Cart routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::get('/sidebar', [CartController::class, 'getSidebar'])->name('sidebar');
        Route::get('/add/{productId}', [CartController::class, 'addAjax'])->name('add.ajax'); // AJAX route
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::put('/{itemId}', [CartController::class, 'update'])->name('update');
        Route::delete('/{itemId}', [CartController::class, 'remove'])->name('remove');
        Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    });
    
    // Checkout routes
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success/{orderId}', [CheckoutController::class, 'success'])->name('success');

    });
    
    // Order routes
    Route::prefix('my-orders')->name('user.orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/pending', [OrderController::class, 'pending'])->name('pending');
        Route::get('/history/{id}', [OrderController::class, 'show'])->name('show');
        Route::get('/pending/{id}', [OrderController::class, 'show'])->name('show_pending');
        Route::post('/{id}/confirm', [OrderController::class, 'confirmDelivery'])->name('confirm');
        Route::get('/{id}/receipt', [OrderController::class, 'downloadReceipt'])->name('download');
    });
    
});
