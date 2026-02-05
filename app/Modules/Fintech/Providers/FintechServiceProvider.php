<?php

namespace Modules\Fintech\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Fintech\Interfaces\WalletRepositoryInterface;
use Modules\Fintech\Repositories\WalletRepository;
use Modules\Fintech\Interfaces\TransactionRepositoryInterface;
use Modules\Fintech\Repositories\TransactionRepository;

/**
 * FintechServiceProvider
 * Enregistre tous les composants du module Fintech
 */
class FintechServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind Repository Interfaces
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        
        // Bind Services as Singletons
        $this->app->singleton(\Modules\Fintech\Services\WalletService::class);
        $this->app->singleton(\Modules\Fintech\Services\TransactionService::class);
        $this->app->singleton(\Modules\Fintech\Services\PaymentGatewayService::class);
    }
    
    public function boot()
    {
        // Routes, Views, Migrations sont gérés par ModuleServiceProvider
    }
}
