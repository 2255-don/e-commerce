<?php

use Illuminate\Support\Facades\Route;
use Modules\Marketplace\Controllers\Api\ProductApiController;
use Modules\Marketplace\Controllers\Api\CartApiController;
use Modules\Marketplace\Controllers\Api\OrderApiController;

/*
|--------------------------------------------------------------------------
| Marketplace API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('marketplace')->group(function () {
    
    // Public product routes
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/{id}', [ProductApiController::class, 'show']);
    Route::post('/products/search', [ProductApiController::class, 'search']);
    
    // Auth-required routes
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // Cart API
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartApiController::class, 'index']);
            Route::post('/add', [CartApiController::class, 'add']);
            Route::put('/{itemId}', [CartApiController::class, 'update']);
            Route::delete('/{itemId}', [CartApiController::class, 'remove']);
        });
        
        // Order API
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderApiController::class, 'index']);
            Route::post('/', [OrderApiController::class, 'create']);
            Route::get('/{id}', [OrderApiController::class, 'show']);
            Route::post('/{id}/cancel', [OrderApiController::class, 'cancel']);
        });
        
    });
    
});
