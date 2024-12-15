<?php

namespace App\Listeners;

use App\Events\Campus\CampusUpdatedOrDeletedEvent;

class CampusWarmupListener extends WarmupListener
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
    public function handle(CampusUpdatedOrDeletedEvent $event): void
    {
        $processCommand = $this->callCommandWarmup(sprintf('campus:warmup %d', $event->campusId()));
        $processCommand->handle();
    }
}
