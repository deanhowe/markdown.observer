<?php

namespace App\Providers;

use App\Contracts\ComposerPackages;
use App\Repositories\ComposerPackagesRepository;
use App\Repositories\ComposerPackagesRepository as ComposerPackagesRepositoryImpl;
use App\Services\ComposerPackagesService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(ComposerPackageStorageServiceProvider::class);

        // Interface binding (for dependency injection)
        $this->app->bind(ComposerPackages::class, ComposerPackagesRepository::class);
        $this->app->singleton(ComposerPackagesRepository::class, ComposerPackagesRepositoryImpl::class);

        $this->mergeConfigFrom(
            __DIR__.'/../../config/composer-packages.php', 'composer-packages'
        );

        // Facade binding
        $this->app->bind('composer.dependencies', function ($app) {
            return $app->make(ComposerPackagesService::class);
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/composer-packages.php' => config_path('composer-packages.php'),
        ], 'composer-packages-config');

    }
}
