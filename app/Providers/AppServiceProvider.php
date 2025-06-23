<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ApiAuthService;
use App\Services\TarificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Enregistrer ApiAuthService dans le conteneur de services
        $this->app->singleton(ApiAuthService::class, function ($app) {
            return new ApiAuthService();
        });

        // Enregistrer TarificationService dans le conteneur de services
        $this->app->singleton(TarificationService::class, function ($app) {
            return new TarificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
