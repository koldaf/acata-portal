<?php

namespace App\Providers;

use App\Models\Members;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::define('access-admin-panel', fn (Members $user) => $user->isAdmin());
        Gate::define('assign-admin-role', fn (Members $user) => $user->isSuperAdmin());
    }
}
