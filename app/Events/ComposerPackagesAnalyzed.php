<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class ComposerPackagesAnalyzed
{
    use Dispatchable, InteractsWithSockets;
    public function __construct(public array $packages) {}
}
