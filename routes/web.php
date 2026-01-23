<?php

use App\Http\Controllers\langue\LanguageController;
use App\Http\Controllers\user\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware(['auth', 'verified']);

Route::get('lang/{locale}', [LanguageController::class, 'swap']);
Route::get('/profile', [UserController::class, 'show'])->name('profile.show')->middleware(['auth']);
Route::post('/profile/update', [UserController::class, 'update'])->name('profile.update')->middleware(['auth']);

// Wallet Routes
Route::group(['prefix' => 'wallet', 'middleware' => 'auth'], function () {
    Route::get('/recharge', [App\Http\Controllers\wallet\WalletController::class, 'showRecharge'])->name('wallet.recharge');
    Route::post('/recharge', [App\Http\Controllers\wallet\WalletController::class, 'processRecharge'])->name('wallet.process-recharge');
});

// Seller/License Routes
Route::group(['prefix' => 'seller', 'middleware' => 'auth'], function () {
    Route::get('/license', [App\Http\Controllers\seller\SellerController::class, 'showLicenseForm'])->name('seller.license');
    Route::post('/license/wallet', [App\Http\Controllers\seller\SellerController::class, 'purchaseWithWallet'])->name('seller.license.wallet');
});

