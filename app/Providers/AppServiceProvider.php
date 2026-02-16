<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Identity\Entities\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin-access', function (User $user) {
            return $user->isSuperAdmin();
        });

        Gate::define('super-admin-access', function (User $user) {
            return $user->isSuperAdmin();
        });
    }
}
