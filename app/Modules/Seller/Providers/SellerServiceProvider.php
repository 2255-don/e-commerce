<?php

namespace Modules\Seller\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Seller\Interfaces\SellerProfileRepositoryInterface;
use Modules\Seller\Repositories\SellerProfileRepository;
use Modules\Seller\Services\SellerProfileService;
use Modules\Seller\Services\SellerStatsService;

class SellerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Repository Interfaces
        $this->app->bind(SellerProfileRepositoryInterface::class, SellerProfileRepository::class);
        
        // Register Services as Singletons
        $this->app->singleton(SellerProfileService::class);
        $this->app->singleton(SellerStatsService::class);
    }
    
    public function boot(): void
    {
        //
    }
}
