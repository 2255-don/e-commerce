<?php

use Illuminate\Support\Facades\Route;
use Modules\Marketplace\Controllers\Web\MarketplaceController;
use Modules\Marketplace\Controllers\Web\CartController;
use Modules\Marketplace\Controllers\Web\CheckoutController;

/*
|--------------------------------------------------------------------------
| Marketplace Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/products/{slug}', [MarketplaceController::class, 'show'])->name('products.show');
Route::get('/marketplace/search', [MarketplaceController::class, 'search'])->name('marketplace.search');

// Auth-required routes
Route::middleware(['auth'])->group(function () {
    
    // Cart routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
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
    
});
