<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PageDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The page filename.
     *
     * @var string
     */
    public $filename;

    /**
     * The page data before deletion.
     *
     * @var array
     */
    public $pageData;

    /**
     * Create a new event instance.
     *
     * @param string $filename
     * @param array $pageData
     * @return void
     */
    public function __construct(string $filename, array $pageData)
    {
        $this->filename = $filename;
        $this->pageData = $pageData;
    }
}
