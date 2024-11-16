<?php

namespace App\Listeners;

use App\Events\Campus\CampusUpdatedOrDeletedEvent;
use App\Jobs\ProcessCommandWarmup;

class CampusWarmupListener
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
        ProcessCommandWarmup::dispatch(sprintf('campus:warmup %d', $event->campusId()));
    }
}
