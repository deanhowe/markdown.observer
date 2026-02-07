<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PageCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The page data.
     *
     * @var array
     */
    public $page;

    /**
     * Create a new event instance.
     *
     * @param array $page
     * @return void
     */
    public function __construct(array $page)
    {
        $this->page = $page;
    }
}
