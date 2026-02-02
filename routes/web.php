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

// Marketplace Routes (Public)
Route::get('/boutique', [App\Http\Controllers\MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/boutique/{product}', [App\Http\Controllers\MarketplaceController::class, 'show'])->name('marketplace.show');

// Cart & Checkout
Route::group(['middleware' => 'auth'], function() {
    Route::get('/cart', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/cart/add/{productId}', [App\Http\Controllers\CheckoutController::class, 'add'])->name('checkout.add');
    Route::get('/cart/details', [App\Http\Controllers\CheckoutController::class, 'cartDetails'])->name('checkout.details');
    Route::post('/cart/update', [App\Http\Controllers\CheckoutController::class, 'update'])->name('checkout.update');
    Route::get('/cart/remove/{productId}', [App\Http\Controllers\CheckoutController::class, 'remove'])->name('checkout.remove'); // Using GET for ease, ideally DELETE
    Route::post('/checkout/process', [App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');

    // Client Orders (History)
    Route::get('/my-orders', [App\Http\Controllers\OrderHistoryController::class, 'index'])->name('user.orders.index');
    Route::get('/my-orders/pending', [App\Http\Controllers\OrderHistoryController::class, 'pending'])->name('user.orders.pending'); 
    
    // Split Show Routes to maintain Sidebar Active State
    Route::get('/my-orders/history/{order}', [App\Http\Controllers\OrderHistoryController::class, 'show'])->name('user.orders.show');
    Route::get('/my-orders/pending/{order}', [App\Http\Controllers\OrderHistoryController::class, 'show'])->name('user.orders.show_pending');
    
    Route::post('/my-orders/{order}/confirm', [App\Http\Controllers\OrderHistoryController::class, 'confirmDelivery'])->name('user.orders.confirm');
    Route::get('/my-orders/{order}/receipt', [App\Http\Controllers\OrderHistoryController::class, 'downloadReceipt'])->name('user.orders.download');
});

// Seller/License Routes
Route::group(['prefix' => 'seller', 'middleware' => 'auth'], function () {
    Route::get('/license', [App\Http\Controllers\seller\SellerController::class, 'showLicenseForm'])->name('seller.license');
    Route::post('/license/wallet', [App\Http\Controllers\seller\SellerController::class, 'purchaseWithWallet'])->name('seller.license.wallet');
    
    // Espace Dashboard (Protected)
    Route::group(['middleware' => [\App\Http\Middleware\EnsureUserIsActiveSeller::class]], function () {
        Route::get('/dashboard', [App\Http\Controllers\seller\SellerEspaceBoutiqueController::class, 'index'])->name('seller.dashboard');
        Route::resource('products', App\Http\Controllers\seller\SellerEspaceBoutiqueController::class, ['as' => 'seller']);
        Route::delete('products/images/{productImageId}', [App\Http\Controllers\seller\SellerEspaceBoutiqueController::class, 'destroyImage'])->name('seller.products.images.destroy');
    });
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

