<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Identity Module
        $this->app->register(\Modules\Identity\Providers\IdentityServiceProvider::class);
        
        // Register Fintech Module (when ready)
        // $this->app->register(\Modules\Fintech\Providers\FintechServiceProvider::class);
        
        // Register Marketplace Module (when ready)
        // $this->app->register(\Modules\Marketplace\Providers\MarketplaceServiceProvider::class);
        
        // Register Seller Module (when ready)
        // $this->app->register(\Modules\Seller\Providers\SellerServiceProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes from all modules
        $this->loadModuleRoutes();
        
        // Load views from all modules
        $this->loadModuleViews();
        
        // Load migrations from all modules
        $this->loadModuleMigrations();
    }

    /**
     * Load routes from all modules
     */
    private function loadModuleRoutes(): void
    {
        $modules = ['Identity', 'Fintech', 'Marketplace', 'Seller'];

        foreach ($modules as $module) {
            $webPath = app_path("Modules/{$module}/Routes/web.php");
            $apiPath = app_path("Modules/{$module}/Routes/api.php");

            if (file_exists($webPath)) {
                $this->loadRoutesFrom($webPath);
            }

            if (file_exists($apiPath)) {
                $this->loadRoutesFrom($apiPath);
            }
        }
    }

    /**
     * Load views from all modules
     */
    private function loadModuleViews(): void
    {
        $modules = ['Identity', 'Fintech', 'Marketplace', 'Seller'];

        foreach ($modules as $module) {
            $viewPath = app_path("Modules/{$module}/Views");

            if (is_dir($viewPath)) {
                $this->loadViewsFrom($viewPath, strtolower($module));
            }
        }
    }

    /**
     * Load migrations from all modules
     */
    private function loadModuleMigrations(): void
    {
        $modules = ['Identity', 'Fintech', 'Marketplace', 'Seller'];

        foreach ($modules as $module) {
            $migrationPath = app_path("Modules/{$module}/Migrations");

            if (is_dir($migrationPath)) {
                $this->loadMigrationsFrom($migrationPath);
            }
        }
    }
}
