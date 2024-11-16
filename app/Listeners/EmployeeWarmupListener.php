<?php

namespace App\Listeners;

use App\Events\Employee\EmployeeUpdateOrDeletedEvent;
use App\Jobs\ProcessCommandWarmup;

class EmployeeWarmupListener
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
    public function handle(EmployeeUpdateOrDeletedEvent $event): void
    {
        ProcessCommandWarmup::dispatch(sprintf('employee:warmup %d', $event->employeeId()));
    }
}
