<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogUserActivity implements ShouldQueue
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
     * Handle the user registered event.
     *
     * @param  \App\Events\UserRegistered  $event
     * @return void
     */
    public function handleUserRegistered(UserRegistered $event)
    {
        Log::info('User registered', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Handle the user logged in event.
     *
     * @param  \App\Events\UserLoggedIn  $event
     * @return void
     */
    public function handleUserLoggedIn(UserLoggedIn $event)
    {
        Log::info('User logged in', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Handle the user logged out event.
     *
     * @param  \App\Events\UserLoggedOut  $event
     * @return void
     */
    public function handleUserLoggedOut(UserLoggedOut $event)
    {
        Log::info('User logged out', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
