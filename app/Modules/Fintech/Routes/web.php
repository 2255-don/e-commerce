<?php

use Illuminate\Support\Facades\Route;
use Modules\Fintech\Controllers\Web\WalletController;

/*
|--------------------------------------------------------------------------
| Fintech Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // Wallet Routes
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/recharge', [WalletController::class, 'recharge'])->name('recharge');
        Route::post('/process-recharge', [WalletController::class, 'processRecharge'])->name('process-recharge');
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
    });
    
});
