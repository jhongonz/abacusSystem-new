<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-03 20:01:10
 */

namespace Core\Employee\Infrastructure\Events;

interface EventDispatcherContract
{
    public function dispatch(object $event): void;
}
