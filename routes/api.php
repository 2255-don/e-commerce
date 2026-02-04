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
    
    // -------------------- PERMISSIONS SYSTEM --------------------
    Route::middleware('can:super-admin-access')->prefix('admin')->group(function () {
        Route::apiResource('modules', \App\Http\Controllers\Api\ModuleController::class);
        Route::apiResource('features', \App\Http\Controllers\Api\FeatureController::class);
        Route::apiResource('permissions', \App\Http\Controllers\Api\PermissionController::class);
    });
    
});
