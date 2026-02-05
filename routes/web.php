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
    
    // ===== WALLET - NOW HANDLED BY FINTECH DDD MODULE =====
    // See: app/Modules/Fintech/Routes/web.php
    // Routes: /wallet/recharge, /wallet/transactions
    // Controllers are in Modules\Fintech\Controllers namespace
    
    // -------------------- CART & CHECKOUT --------------------
    Route::prefix('cart')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::get('/add/{productId}', [CheckoutController::class, 'add'])->name('add');
        Route::get('/details', [CheckoutController::class, 'cartDetails'])->name('details');
        Route::post('/update', [CheckoutController::class, 'update'])->name('update');
        Route::get('/remove/{productId}', [CheckoutController::class, 'remove'])->name('remove');
    });
    
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    
    // -------------------- ORDERS --------------------
    Route::prefix('my-orders')->name('user.orders.')->group(function () {
        Route::get('/', [OrderHistoryController::class, 'index'])->name('index');
        Route::get('/pending', [OrderHistoryController::class, 'pending'])->name('pending');
        
        // Split Show Routes to maintain Sidebar Active State
        Route::get('/history/{order}', [OrderHistoryController::class, 'show'])->name('show');
        Route::get('/pending/{order}', [OrderHistoryController::class, 'show'])->name('show_pending');
        
        Route::post('/{order}/confirm', [OrderHistoryController::class, 'confirmDelivery'])->name('confirm');
        Route::get('/{order}/receipt', [OrderHistoryController::class, 'downloadReceipt'])->name('download');
    });
    
    // ===== SELLER - NOW HANDLED BY SELLER DDD MODULE =====
    // See: app/Modules/Seller/Routes/web.php
    // Routes: /seller/dashboard, /seller/products/*
    // Controllers are in Modules\Seller\Controllers namespace
    // Middleware: defined in Seller module
    
    
    // -------------------- ADMIN --------------------
    Route::middleware('can:admin-access')->prefix('admin')->name('admin.')->group(function () {
        // KYC Management
        Route::prefix('kyc')->name('kyc.')->group(function () {
            Route::get('/', [AdminKycController::class, 'index'])->name('index');
            Route::post('/{user}/approve', [AdminKycController::class, 'approve'])->name('approve');
            Route::post('/{user}/reject', [AdminKycController::class, 'reject'])->name('reject');
        });
    });

    // -------------------- SUPER ADMIN --------------------
    Route::middleware('can:super-admin-access')->prefix('admin')->name('admin.')->group(function () {
        // Profils Management
        Route::resource('profils', \App\Http\Controllers\Admin\ProfilController::class);
        
        // Roles Management (CRUD)
        Route::resource('roles', \App\Http\Controllers\Admin\RoleManagementController::class);
        
        // Permissions System Management
        Route::resource('modules', \App\Http\Controllers\Web\ModuleController::class);
        Route::resource('features', \App\Http\Controllers\Web\FeatureController::class);
        Route::resource('permissions', \App\Http\Controllers\Web\PermissionController::class);
        
        // Role Permissions Management
        Route::get('roles/{role}/permissions', [\App\Http\Controllers\Web\RoleController::class, 'permissions'])->name('roles.permissions');
        Route::post('roles/{role}/permissions', [\App\Http\Controllers\Web\RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
        
        // User Management - Role Assignment
        Route::get('users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
        Route::get('users/{user}/roles', [\App\Http\Controllers\Admin\UserManagementController::class, 'roles'])->name('users.roles');
        Route::post('users/{user}/roles', [\App\Http\Controllers\Admin\UserManagementController::class, 'updateRoles'])->name('users.roles.update');
    });
    
});
