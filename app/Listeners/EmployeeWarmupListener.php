<?php

namespace App\Listeners;

use App\Events\Employee\EmployeeUpdateOrDeletedEvent;

class EmployeeWarmupListener extends WarmupListener
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
        $processCommand = $this->callCommandWarmup(sprintf('employee:warmup %d', $event->employeeId()));
        $processCommand->handle();
    }
}
