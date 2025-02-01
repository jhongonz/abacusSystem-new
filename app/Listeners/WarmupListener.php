<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-15 19:06:01
 */

namespace App\Listeners;

use App\Jobs\ProcessCommandWarmup;

abstract class WarmupListener
{
    /**
     * @param string|array<string> $command
     */
    protected function callCommandWarmup(string|array $command): ProcessCommandWarmup
    {
        return new ProcessCommandWarmup($command);
    }
}
