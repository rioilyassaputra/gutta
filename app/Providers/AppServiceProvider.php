<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Observers\OrderObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Services\PaymentServiceInterface::class,
            \App\Services\PakasirPaymentService::class
        );
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);

        // Share active categories globally with all views
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            // Check if categories table exists before querying to prevent migration errors
            if (\Illuminate\Support\Facades\Schema::hasTable('categories')) {
                $view->with('globalCategories', \App\Models\Category::all());
            } else {
                $view->with('globalCategories', collect());
            }
        });

        // Register rate limiters for sensitive endpoints (Brute Force & DoS prevention)
        RateLimiter::for('sensitive-auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('promo-code', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
