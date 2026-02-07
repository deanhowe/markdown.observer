<?php

namespace App\Listeners;

use App\Events\RevisionCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogRevisionActivity implements ShouldQueue
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
     * Handle the revision created event.
     *
     * @param  \App\Events\RevisionCreated  $event
     * @return void
     */
    public function handleRevisionCreated(RevisionCreated $event)
    {
        Log::info('Revision created', [
            'filename' => $event->revision->filename,
            'revision_type' => $event->revision->revision_type,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
