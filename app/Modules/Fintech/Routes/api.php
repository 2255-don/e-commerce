<?php

use Illuminate\Support\Facades\Route;
use Modules\Fintech\Controllers\Api\WalletApiController;

/*
|--------------------------------------------------------------------------
| Fintech API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('wallet')->group(function () {
    
    // Wallet API
    Route::get('/balance', [WalletApiController::class, 'balance']);
    Route::get('/transactions', [WalletApiController::class, 'transactions']);
    Route::post('/transfer', [WalletApiController::class, 'transfer']);
    Route::get('/transactions/{reference}', [WalletApiController::class, 'getTransaction']);
    
});
