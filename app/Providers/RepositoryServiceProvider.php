<?php

namespace App\Providers;

use App\Repositories\CachedPageRepository;
use App\Repositories\CachedPageRevisionRepository;
use App\Repositories\Interfaces\PageRepositoryInterface;
use App\Repositories\Interfaces\PageRevisionRepositoryInterface;
use App\Repositories\PageRepository;
use App\Repositories\PageRevisionRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the concrete repository implementations
        $this->app->bind(PageRepository::class, PageRepository::class);
        $this->app->bind(PageRevisionRepository::class, PageRevisionRepository::class);

        // Bind the interfaces to the cached implementations
        $this->app->bind(PageRepositoryInterface::class, CachedPageRepository::class);
        $this->app->bind(PageRevisionRepositoryInterface::class, CachedPageRevisionRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
