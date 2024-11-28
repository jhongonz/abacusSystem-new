<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-10-13 21:05:04
 */

namespace App\Traits;

use App\Http\Exceptions\RouteNotFoundException;
use Assert\Assertion;
use Assert\AssertionFailedException;

trait RouterTrait
{
    /**
     * @throws RouteNotFoundException
     * @throws AssertionFailedException
     */
    protected function validateRoute(string $route): void
    {
        $routesCollection = $this->router->getRoutes();
        $routes = $routesCollection->getRoutes();

        $slugs = [];
        foreach ($routes as $item) {
            $method = $item->methods();

            if ('GET' === $method[0]) {
                $slugs[] = $item->uri();
            }
        }

        $slugs = array_unique($slugs);
        Assertion::inArray($route, $slugs);
    }
}
