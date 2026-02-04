<?php

namespace Modules\Identity\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Identity\Services\FeatureAccessService;
use Modules\Identity\Services\FeatureService;
use Modules\Identity\Services\ModuleService;
use Modules\Identity\Services\PermissionService;

class IdentityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Services as singletons
        $this->app->singleton(FeatureAccessService::class);
        $this->app->singleton(FeatureService::class);
        $this->app->singleton(ModuleService::class);
        $this->app->singleton(PermissionService::class);
        
        // Bind Repositories (when created)
        // $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Routes, Migrations, Views are loaded by ModuleServiceProvider
    }
}
