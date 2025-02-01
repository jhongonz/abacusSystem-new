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
    private function validateRoute(string $route): bool
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

        return Assertion::inArray($route, $slugs);
    }
}
