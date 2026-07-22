<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\NewsProviderInterface::class,
            \App\Services\News\NewsApiService::class
        );
        $this->app->bind(
            \App\Contracts\NewsRepositoryInterface::class,
            \App\Repositories\NewsRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
     Paginator::useBootstrapFive();

     URL::forceScheme('https');
     
    }
}
