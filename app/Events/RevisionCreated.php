<?php

namespace App\Events;

use App\Models\PageRevision;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RevisionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The page revision instance.
     *
     * @var \App\Models\PageRevision
     */
    public $revision;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\PageRevision $revision
     * @return void
     */
    public function __construct(PageRevision $revision)
    {
        $this->revision = $revision;
    }
}
