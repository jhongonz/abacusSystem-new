<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-03 20:02:14
 */

namespace Core\Employee\Infrastructure\Events;

class EventDispatcher implements EventDispatcherContract
{
    public function dispatch(object $event): void
    {
        event($event);
    }
}
