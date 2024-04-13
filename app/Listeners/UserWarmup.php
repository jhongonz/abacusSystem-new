<?php

namespace App\Listeners;

use App\Events\User\UserUpdateOrDeleteEvent;
use App\Jobs\ProcessCommandWarmup;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
     * @param UserUpdateOrDeleteEvent $event
     */
    public function handle(UserUpdateOrDeleteEvent $event): void
    {
        ProcessCommandWarmup::dispatch('user:warmup '.$event->userId()->value());
    }
}
