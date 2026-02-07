<?php

namespace App\Listeners;

use App\Events\PageCreated;
use App\Events\PageDeleted;
use App\Events\PageUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogPageActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the page created event.
     *
     * @param  \App\Events\PageCreated  $event
     * @return void
     */
    public function handlePageCreated(PageCreated $event)
    {
        Log::info('Page created', [
            'filename' => $event->page['filename'],
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Handle the page updated event.
     *
     * @param  \App\Events\PageUpdated  $event
     * @return void
     */
    public function handlePageUpdated(PageUpdated $event)
    {
        Log::info('Page updated', [
            'filename' => $event->page['filename'],
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Handle the page deleted event.
     *
     * @param  \App\Events\PageDeleted  $event
     * @return void
     */
    public function handlePageDeleted(PageDeleted $event)
    {
        Log::info('Page deleted', [
            'filename' => $event->filename,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
