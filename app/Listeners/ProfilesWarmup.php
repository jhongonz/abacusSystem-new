<?php

namespace App\Listeners;

use App\Events\Profile\ModuleUpdatedOrDeletedEvent;
use App\Events\Profile\ProfileUpdatedOrDeletedEvent;
use App\Jobs\ProcessCommandWarmup;

class ProfilesWarmup
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
        ProcessCommandWarmup::dispatch('profile:warmup');
        ProcessCommandWarmup::dispatch('module:warmup');
    }
}
