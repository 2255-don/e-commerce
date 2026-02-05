<?php

namespace Modules\Marketplace\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Marketplace\Interfaces\ProductRepositoryInterface;
use Modules\Marketplace\Repositories\ProductRepository;
use Modules\Marketplace\Interfaces\CategoryRepositoryInterface;
use Modules\Marketplace\Repositories\CategoryRepository;
use Modules\Marketplace\Interfaces\CartRepositoryInterface;
use Modules\Marketplace\Repositories\CartRepository;
use Modules\Marketplace\Interfaces\OrderRepositoryInterface;
use Modules\Marketplace\Repositories\OrderRepository;
use Modules\Marketplace\Services\CartService;
use Modules\Marketplace\Services\OrderService;
use Modules\Marketplace\Services\StockService;
use Modules\Marketplace\Services\PricingService;

class MarketplaceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Repository Interfaces
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        
        // Register Services as Singletons
        $this->app->singleton(CartService::class);
        $this->app->singleton(OrderService::class);
        $this->app->singleton(StockService::class);
        $this->app->singleton(PricingService::class);
    }
    
    public function boot(): void
    {
        //
    }
}
