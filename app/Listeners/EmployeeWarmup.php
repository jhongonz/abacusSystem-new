<?php

namespace App\Listeners;

use App\Events\Employee\EmployeeUpdateOrDeletedEvent;
use App\Jobs\ProcessCommandWarmup;

/**
 * @codeCoverageIgnore
 */
class EmployeeWarmup
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
        ProcessCommandWarmup::dispatch('employee:warmup '.$event->employeeId());
    }
}
