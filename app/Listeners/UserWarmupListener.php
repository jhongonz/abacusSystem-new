<?php

namespace App\Listeners;

use App\Events\User\UserUpdateOrDeleteEvent;
use App\Jobs\ProcessCommandWarmup;

class UserWarmupListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(UserUpdateOrDeleteEvent $event): void
    {
        ProcessCommandWarmup::dispatch(sprintf('user:warmup %d', $event->userId()));
    }
}
