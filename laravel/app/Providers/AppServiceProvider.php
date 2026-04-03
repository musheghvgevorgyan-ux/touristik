<?php

namespace App\Providers;

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
        // Provide default $title to all views so controllers don't need to pass it
        view()->composer('*', function ($view) {
            if (!isset($view->getData()['title'])) {
                $view->with('title', config('app.name', 'Touristik'));
            }
        });
    }
}
