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

// KYC Routes
Route::group(['prefix' => 'kyc', 'middleware' => 'auth'], function () {
    Route::get('/', [App\Http\Controllers\user\KycController::class, 'showForm'])->name('kyc.form');
    Route::post('/', [App\Http\Controllers\user\KycController::class, 'store'])->name('kyc.store');
});

// Admin Routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'can:admin-access']], function () {
    Route::get('/kyc', [App\Http\Controllers\admin\AdminKycController::class, 'index'])->name('admin.kyc.index');
    Route::post('/kyc/{user}/approve', [App\Http\Controllers\admin\AdminKycController::class, 'approve'])->name('admin.kyc.approve');
    Route::post('/kyc/{user}/reject', [App\Http\Controllers\admin\AdminKycController::class, 'reject'])->name('admin.kyc.reject');
});

