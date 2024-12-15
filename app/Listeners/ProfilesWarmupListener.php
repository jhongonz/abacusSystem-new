<?php

namespace App\Listeners;

use App\Events\Profile\ModuleUpdatedOrDeletedEvent;
use App\Events\Profile\ProfileUpdatedOrDeletedEvent;

class ProfilesWarmupListener extends WarmupListener
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
    public function handle(ProfileUpdatedOrDeletedEvent|ModuleUpdatedOrDeletedEvent $event): void
    {
        $processCommandWarmup = $this->callCommandWarmup(['profile:warmup', 'module:warmup']);
        $processCommandWarmup->handle();
    }
}
