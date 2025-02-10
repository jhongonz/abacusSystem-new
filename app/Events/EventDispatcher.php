<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-03 20:35:11
 */

namespace App\Events;

use Illuminate\Contracts\Events\Dispatcher;

class EventDispatcher
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {
    }

    public function dispatch(object $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}
