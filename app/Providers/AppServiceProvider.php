<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Service\API\Service\Service;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
        $this->app->bind(\Service\API\Service::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
