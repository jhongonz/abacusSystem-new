<?php

namespace App\Listeners;

use App\Events\User\UserUpdateOrDeleteEvent;
use App\Jobs\ProcessCommandWarmup;

class UserWarmup
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserUpdateOrDeleteEvent $event): void
    {
        ProcessCommandWarmup::dispatch('user:warmup '.$event->userId()->value());
    }
}
