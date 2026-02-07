<?php

namespace App\Listeners;

use App\Events\RevisionCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class CacheRevisionData implements ShouldQueue
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
        $this->cacheRevisionData($event->revision);
        $this->invalidateRevisionListCache($event->revision->filename);
    }

    /**
     * Cache revision data.
     *
     * @param  \App\Models\PageRevision  $revision
     * @return void
     */
    private function cacheRevisionData($revision)
    {
        $cacheKey = 'revision_' . $revision->id;
        Cache::put($cacheKey, $revision, now()->addHours(24));

        // Also cache as latest revision for this file
        $latestCacheKey = 'latest_revision_' . $revision->filename;
        Cache::put($latestCacheKey, $revision, now()->addHours(24));
    }

    /**
     * Invalidate revision list cache for a file.
     *
     * @param  string  $filename
     * @return void
     */
    private function invalidateRevisionListCache(string $filename)
    {
        $cacheKey = 'revisions_list_' . $filename;
        Cache::forget($cacheKey);
    }
}
