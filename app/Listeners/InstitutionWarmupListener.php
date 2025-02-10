<?php

namespace App\Listeners;

use App\Events\Institution\InstitutionUpdateOrDeletedEvent;

class InstitutionWarmupListener extends WarmupListener
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
    public function handle(InstitutionUpdateOrDeletedEvent $event): void
    {
        $processCommand = $this->callCommandWarmup(sprintf('institution:warmup %d', $event->institutionId()));
        $processCommand->handle();
    }
}
