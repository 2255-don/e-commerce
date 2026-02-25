<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ============================================
// CONTROLLERS API - Groupés par domaine
// ============================================

// Auth
use App\Http\Controllers\Api\Auth\AuthApiController;

// Wallet
use App\Http\Controllers\Api\Wallet\WalletApiController;

// Seller
use App\Http\Controllers\Api\Seller\SellerApiController;

// Mock
use App\Http\Controllers\mock\MockMobileMoneyController;

// ============================================
// ROUTES PUBLIQUES
// ============================================

// Auth
Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('/register', [AuthApiController::class, 'register'])->name('register');
    Route::post('/login', [AuthApiController::class, 'login'])->name('login');
    Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword'])->name('password.email');
    Route::post('/reset-password', [AuthApiController::class, 'resetPassword'])->name('password.update');
});

// Mock Mobile Money (pour tests)
Route::post('/mock/mobile-money/pay', [MockMobileMoneyController::class, 'pay']);

// ============================================
// ROUTES AUTHENTIFIÉES (Sanctum)
// ============================================

Route::middleware('auth:sanctum')->group(function () {
    
    // User info
    Route::get('/user', [AuthApiController::class, 'me']);
    Route::post('/logout', [AuthApiController::class, 'logout'])->name('api.auth.logout');
    
    // -------------------- WALLET --------------------
    Route::prefix('wallet')->name('api.wallet.')->group(function () {
        Route::get('/', [WalletApiController::class, 'index'])->name('index');
        Route::post('/recharge', [WalletApiController::class, 'recharge'])->name('recharge');
    });
    
    // -------------------- SELLER --------------------
    Route::prefix('seller')->name('api.seller.')->group(function () {
        Route::get('/profile', [SellerApiController::class, 'index'])->name('profile');
        Route::post('/license', [SellerApiController::class, 'purchaseLicense'])->name('license');
    });
    
    // -------------------- MARKETPLACE --------------------
    Route::prefix('marketplace')->name('api.marketplace.')->group(function () {
        Route::get('/products', [\App\Http\Controllers\Api\Marketplace\MarketplaceApiController::class, 'index'])->name('products.index');
        Route::get('/products/{id}', [\App\Http\Controllers\Api\Marketplace\MarketplaceApiController::class, 'show'])->name('products.show');
    });

    // -------------------- CART --------------------
    Route::prefix('cart')->name('api.cart.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Cart\CartApiController::class, 'index'])->name('index');
        Route::post('/add', [\App\Http\Controllers\Api\Cart\CartApiController::class, 'add'])->name('add');
        Route::put('/update/{productId}', [\App\Http\Controllers\Api\Cart\CartApiController::class, 'update'])->name('update');
        Route::delete('/remove/{productId}', [\App\Http\Controllers\Api\Cart\CartApiController::class, 'remove'])->name('remove');
        Route::delete('/clear', [\App\Http\Controllers\Api\Cart\CartApiController::class, 'clear'])->name('clear');
    });

    // -------------------- ORDERS --------------------
    Route::prefix('orders')->name('api.orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Order\OrderApiController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Api\Order\OrderApiController::class, 'show'])->name('show');
        Route::post('/{id}/confirm', [\App\Http\Controllers\Api\Order\OrderApiController::class, 'confirm'])->name('confirm');
        Route::post('/{id}/report', [\App\Http\Controllers\Api\Order\OrderApiController::class, 'reportIssue'])->name('report');
    });

    // -------------------- ADMIN --------------------
    Route::middleware('can:super-admin-access')->prefix('admin')->group(function () {
        Route::apiResource('modules', \App\Http\Controllers\Api\ModuleController::class);
        Route::apiResource('features', \App\Http\Controllers\Api\FeatureController::class);
        Route::apiResource('permissions', \App\Http\Controllers\Api\PermissionController::class);

        // Admin Order Actions
        Route::post('/orders/{id}/refund', [\App\Http\Controllers\Api\Admin\AdminOrderApiController::class, 'refund'])->name('api.admin.orders.refund');
    });
});
