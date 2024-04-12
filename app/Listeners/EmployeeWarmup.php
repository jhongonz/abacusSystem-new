<?php

namespace App\Listeners;

use App\Events\Employee\EmployeeUpdateOrDeletedEvent;
use App\Jobs\ProcessCommandWarmup;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EmployeeWarmup
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
     * @param EmployeeUpdateOrDeletedEvent $event
     */
    public function handle(EmployeeUpdateOrDeletedEvent $event): void
    {
        ProcessCommandWarmup::dispatch('employee:warmup '.$event->employeeId()->value());
    }
}
