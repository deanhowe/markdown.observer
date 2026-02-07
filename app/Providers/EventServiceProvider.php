<?php

namespace App\Providers;

use App\Events\PageCreated;
use App\Events\PageDeleted;
use App\Events\PageUpdated;
use App\Events\RevisionCreated;
use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Events\UserRegistered;
use App\Listeners\CachePageData;
use App\Listeners\CacheRevisionData;
use App\Listeners\LogPageActivity;
use App\Listeners\LogRevisionActivity;
use App\Listeners\LogUserActivity;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        PageCreated::class => [
            LogPageActivity::class . '@handlePageCreated',
            CachePageData::class . '@handlePageCreated',
        ],
        PageUpdated::class => [
            LogPageActivity::class . '@handlePageUpdated',
            CachePageData::class . '@handlePageUpdated',
        ],
        PageDeleted::class => [
            LogPageActivity::class . '@handlePageDeleted',
            CachePageData::class . '@handlePageDeleted',
        ],
        RevisionCreated::class => [
            LogRevisionActivity::class . '@handleRevisionCreated',
            CacheRevisionData::class . '@handleRevisionCreated',
        ],
        UserRegistered::class => [
            LogUserActivity::class . '@handleUserRegistered',
        ],
        UserLoggedIn::class => [
            LogUserActivity::class . '@handleUserLoggedIn',
        ],
        UserLoggedOut::class => [
            LogUserActivity::class . '@handleUserLoggedOut',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
