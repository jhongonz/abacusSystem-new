<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-15 16:35:27
 */

namespace App\Jobs;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class CommandWarmup implements ShouldQueue
{
    protected Kernel $artisan;

    public function __construct()
    {
        $this->artisan = app(Kernel::class);
    }
}
