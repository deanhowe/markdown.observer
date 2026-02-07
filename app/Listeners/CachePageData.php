<?php

namespace App\Listeners;

use App\Events\PageCreated;
use App\Events\PageDeleted;
use App\Events\PageUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class CachePageData implements ShouldQueue
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
        $this->cachePageData($event->page);
        $this->invalidatePageListCache();
    }

    /**
     * Handle the page updated event.
     *
     * @param  \App\Events\PageUpdated  $event
     * @return void
     */
    public function handlePageUpdated(PageUpdated $event)
    {
        $this->cachePageData($event->page);
    }

    /**
     * Handle the page deleted event.
     *
     * @param  \App\Events\PageDeleted  $event
     * @return void
     */
    public function handlePageDeleted(PageDeleted $event)
    {
        $this->invalidatePageCache($event->filename);
        $this->invalidatePageListCache();
    }

    /**
     * Cache page data.
     *
     * @param  array  $page
     * @return void
     */
    private function cachePageData(array $page)
    {
        $cacheKey = 'page_' . $page['filename'];
        Cache::put($cacheKey, $page, now()->addHours(24));
    }

    /**
     * Invalidate page cache.
     *
     * @param  string  $filename
     * @return void
     */
    private function invalidatePageCache(string $filename)
    {
        $cacheKey = 'page_' . $filename;
        Cache::forget($cacheKey);
    }

    /**
     * Invalidate page list cache.
     *
     * @return void
     */
    private function invalidatePageListCache()
    {
        Cache::forget('pages_list');
    }
}
