<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ============================================
// CONTROLLERS API - Groupés par domaine
// ============================================

// Auth
use App\Http\Controllers\Api\Auth\AuthApiController;

// LEGACY CONTROLLERS - Commented out (migrated to DDD modules)
// use App\Http\Controllers\Api\Wallet\WalletApiController;
// use App\Http\Controllers\Api\Seller\SellerApiController;

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
    
    // ===== WALLET API - NOW HANDLED BY FINTECH DDD MODULE =====
    // See: app/Modules/Fintech/Routes/api.php
    // Routes: /api/wallet/*, /api/wallet/transactions, etc.
    
    // ===== SELLER API - NOW HANDLED BY SELLER DDD MODULE =====
    // See: app/Modules/Seller/Routes/api.php
    // Routes: /api/seller/profile, /api/seller/products, etc.
    
    // -------------------- PERMISSIONS SYSTEM --------------------
    Route::middleware('can:super-admin-access')->prefix('admin')->group(function () {
        Route::apiResource('modules', \App\Http\Controllers\Api\ModuleController::class);
        Route::apiResource('features', \App\Http\Controllers\Api\FeatureController::class);
        Route::apiResource('permissions', \App\Http\Controllers\Api\PermissionController::class);
    });
    
});
