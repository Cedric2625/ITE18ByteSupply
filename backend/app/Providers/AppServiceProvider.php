<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Admin;
use App\Models\Buyer;
use App\Models\Order;
use App\Policies\BuyerPolicy;
use App\Policies\OrderPolicy;

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
        // Simple authorization gates for API routes
        Gate::define('admin-only', function ($user): bool {
            // Authorize only Admin model instances
            return $user instanceof Admin;
        });

        // Register policies
        Gate::policy(Buyer::class, BuyerPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
    }
}
