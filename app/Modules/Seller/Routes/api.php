<?php

use Illuminate\Support\Facades\Route;
use Modules\Seller\Controllers\Api\SellerProfileApiController;
use Modules\Seller\Controllers\Api\SellerProductApiController;

/*
|--------------------------------------------------------------------------
| Seller API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('seller')->group(function () {
    
    // Profile Management
    Route::prefix('profile')->group(function () {
        Route::get('/', [SellerProfileApiController::class, 'show']);
        Route::post('/', [SellerProfileApiController::class, 'create']);
        Route::put('/', [SellerProfileApiController::class, 'update']);
        Route::get('/stats', [SellerProfileApiController::class, 'stats']);
    });
    
    // Products Management
    Route::prefix('products')->group(function () {
        Route::get('/', [SellerProductApiController::class, 'index']);
        Route::post('/', [SellerProductApiController::class, 'store']);
        Route::get('/{id}', [SellerProductApiController::class, 'show']);
        Route::put('/{id}', [SellerProductApiController::class, 'update']);
        Route::post('/{id}/stock', [SellerProductApiController::class, 'updateStock']);
    });
    
});
