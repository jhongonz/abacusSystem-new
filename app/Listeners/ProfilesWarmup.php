<?php

namespace App\Listeners;

use App\Events\Profile\ModuleUpdatedOrDeletedEvent;
use App\Jobs\ProcessCommandWarmup;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProfilesWarmup
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
public function handle(ModuleUpdatedOrDeletedEvent $event): void
    {
        ProcessCommandWarmup::dispatch('profile:warmup');
        ProcessCommandWarmup::dispatch('module:warmup');
    }
}
