<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-10-13 21:05:04
 */

namespace App\Traits;

use App\Http\Exceptions\RouteNotFoundException;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

trait RouterTrait
{
    public function setRouter(Router $router): void
    {
        $this->router = $router;
    }

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

            if ($method[0] === 'GET') {
                $slugs[] = $item->uri();
            }
        }

        $slugs = array_unique($slugs);
        Assertion::inArray($route, $slugs);
    }
}
