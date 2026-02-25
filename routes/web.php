<?php

use Illuminate\Support\Facades\Route;

// ============================================
// CONTROLLERS WEB - Groupés par domaine
// ============================================

// Langue
use App\Http\Controllers\langue\LanguageController;

// Marketplace
use App\Http\Controllers\Web\Marketplace\MarketplaceController;

// Cart & Checkout
use App\Http\Controllers\Web\Cart\CheckoutController;

// Orders
use App\Http\Controllers\Web\Order\OrderHistoryController;

// User
use App\Http\Controllers\Web\User\UserController;
use App\Http\Controllers\Web\User\KycController;

// Seller
use App\Http\Controllers\Web\Seller\SellerController;
use App\Http\Controllers\Web\Seller\ProductController as SellerProductController;

// Wallet
use App\Http\Controllers\Web\Wallet\WalletController;

// Admin
use App\Http\Controllers\Web\Admin\KycController as AdminKycController;

// Middleware
use App\Http\Middleware\EnsureUserIsActiveSeller;

// ============================================
// ROUTES PUBLIQUES
// ============================================

Route::get('/', function () {
    return view('welcome');
});

// Language Switcher
Route::get('lang/{locale}', [LanguageController::class, 'swap']);

// Marketplace (accessible sans login)
Route::prefix('boutique')->name('marketplace.')->group(function () {
    Route::get('/', [MarketplaceController::class, 'index'])->name('index');
    Route::get('/{product}', [MarketplaceController::class, 'show'])->name('show');
});

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
    
    // -------------------- WALLET --------------------
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/recharge', [WalletController::class, 'showRecharge'])->name('recharge');
        Route::post('/recharge', [WalletController::class, 'processRecharge'])->name('process-recharge');

        // Withdrawal
        Route::get('/withdraw', [WalletController::class, 'showWithdraw'])->name('withdraw');
        Route::post('/withdraw', [WalletController::class, 'processWithdraw'])->name('process-withdraw');
    });
    
    // -------------------- CART & CHECKOUT --------------------
    Route::prefix('cart')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::get('/add/{productId}', [CheckoutController::class, 'add'])->name('add');
        Route::get('/details', [CheckoutController::class, 'cartDetails'])->name('details');
        Route::post('/update/{productId}', [CheckoutController::class, 'update'])->name('update');
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
        Route::post('/{order}/refund', [OrderHistoryController::class, 'reportIssue'])->name('refund');
    });
    
    // -------------------- SELLER --------------------
    Route::prefix('seller')->name('seller.')->group(function () {
        // License (accessible à tous les users authentifiés)
        Route::get('/license', [SellerController::class, 'showLicenseForm'])->name('license');
        Route::post('/license/wallet', [SellerController::class, 'purchaseWithWallet'])->name('license.wallet');
        
        // Dashboard & Products (seulement pour vendeurs actifs)
        Route::middleware(EnsureUserIsActiveSeller::class)->group(function () {
            Route::get('/dashboard', [SellerProductController::class, 'index'])->name('dashboard');
            Route::resource('products', SellerProductController::class);
            Route::delete('products/images/{productImageId}', [SellerProductController::class, 'destroyImage'])->name('products.images.destroy');
            
            // Orders Management
            Route::get('/orders', [\App\Http\Controllers\Web\Seller\SellerOrderController::class, 'index'])->name('orders.index');
            Route::post('/orders/{order}/ship', [\App\Http\Controllers\Web\Seller\SellerOrderController::class, 'markAsShipped'])->name('orders.ship');
        });
    });
    
    
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
        
        // Orders Management
        Route::get('orders', [\App\Http\Controllers\Web\Admin\AdminOrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/{order}/refund', [App\Http\Controllers\Web\Admin\AdminOrderController::class, 'refund'])->name('orders.refund');
    });
    
});
