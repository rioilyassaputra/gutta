<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Order;
use App\Observers\OrderObserver;

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
    }
}
