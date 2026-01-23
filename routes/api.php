<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    // Wallet API
    Route::get('/wallet', [App\Http\Controllers\Api\WalletApiController::class, 'index']);
    Route::post('/wallet/recharge', [App\Http\Controllers\Api\WalletApiController::class, 'recharge']);

    // Seller API
    Route::get('/seller/profile', [App\Http\Controllers\Api\SellerApiController::class, 'index']);
    Route::post('/seller/license', [App\Http\Controllers\Api\SellerApiController::class, 'purchaseLicense']);
});

// Mock Mobile Money API
Route::post('/mock/mobile-money/pay', [App\Http\Controllers\mock\MockMobileMoneyController::class, 'pay']);
