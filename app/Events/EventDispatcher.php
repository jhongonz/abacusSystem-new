<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-03 20:35:11
 */

namespace App\Events;

class EventDispatcher
{
    public function dispatch(object $event): void
    {
        event($event);
    }
}
