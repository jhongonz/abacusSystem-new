<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-15 19:06:01
 */

namespace App\Listeners;

use App\Jobs\ProcessCommandWarmup;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class WarmupListener implements ShouldQueue
{
    /**
     * The name of the connection the job should be sent to.
     *
     * @var string|null
     */
    public ?string $connection = 'rabbitmq';

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public ?string $queue = 'warmupListener';

    /**
     * @param string|array<string> $command
     */
    protected function callCommandWarmup(string|array $command): ProcessCommandWarmup
    {
        return new ProcessCommandWarmup($command);
    }
}
