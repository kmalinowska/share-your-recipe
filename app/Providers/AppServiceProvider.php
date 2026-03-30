<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

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
        // use global category for navbar/sidebar
        View::composer('*', function($view) {
            $navCategories = Category::orderBy('name')->get();
            $view->with('navCategories', $navCategories);
        });
    }
}
