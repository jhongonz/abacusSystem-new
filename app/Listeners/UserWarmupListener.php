<?php

namespace App\Listeners;

use App\Events\User\UserUpdateOrDeleteEvent;

class UserWarmupListener extends WarmupListener
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
        $processCommand = $this->callCommandWarmup(sprintf('user:warmup %d', $event->userId()));
        $processCommand->handle();
    }
}
