<?php

use Illuminate\Support\Facades\Route;

// ============================================
// CONTROLLERS WEB - Groupés par domaine
// ============================================

// Langue
use App\Http\Controllers\langue\LanguageController;

// LEGACY CONTROLLERS - Commented out (migrated to DDD modules)
// use App\Http\Controllers\Web\Marketplace\MarketplaceController;
// use App\Http\Controllers\Web\Seller\SellerController;
// use App\Http\Controllers\Web\Seller\ProductController as SellerProductController;
// use App\Http\Controllers\Web\Wallet\WalletController;

// Cart & Checkout (NOT YET MIGRATED)
use App\Http\Controllers\Web\Cart\CheckoutController;

// Orders (NOT YET MIGRATED)
use App\Http\Controllers\Web\Order\OrderHistoryController;

// User (NOT YET MIGRATED)
use App\Http\Controllers\Web\User\UserController;
use App\Http\Controllers\Web\User\KycController;

// Admin
use App\Http\Controllers\Web\Admin\KycController as AdminKycController;

// Middleware (LEGACY - now in DDD modules)
// use App\Http\Middleware\EnsureUserIsActiveSeller;

// ============================================
// ROUTES PUBLIQUES
// ============================================

Route::get('/', function () {
    return view('welcome');
});

// Language Switcher
Route::get('lang/{locale}', [LanguageController::class, 'swap']);

// ===== MARKETPLACE - NOW HANDLED BY DDD MODULE =====
// See: app/Modules/Marketplace/Routes/web.php
// Routes: /marketplace, /marketplace/{product}, /cart, /checkout
// Controllers are in Modules\Marketplace\Controllers namespace

// ============================================
// ROUTES AUTHENTIFIÉES
// ============================================

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // -------------------- USER --------------------
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'show'])->name('profile.show');
        Route::post('/update', [UserController::class, 'update'])->name('profile.update');
    });
    
    // -------------------- KYC --------------------
    Route::prefix('kyc')->name('kyc.')->group(function () {
        Route::get('/', [KycController::class, 'showForm'])->name('form');
        Route::post('/', [KycController::class, 'store'])->name('store');
    });
    
    // -------------------- ADMIN KYC --------------------
    Route::prefix('admin/kyc')->name('admin.kyc.')->group(function () {
        Route::get('/', [AdminKycController::class, 'index'])->name('index');
        Route::post('/{user}/approve', [AdminKycController::class, 'approve'])->name('approve');
        Route::post('/{user}/reject', [AdminKycController::class, 'reject'])->name('reject');
    });
    
    });
    

