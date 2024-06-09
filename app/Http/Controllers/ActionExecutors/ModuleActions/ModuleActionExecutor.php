<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-09 10:54:13
 */

namespace App\Http\Controllers\ActionExecutors\ModuleActions;

use App\Http\Controllers\ActionExecutors\ActionExecutor;
use App\Http\Exceptions\RouteNotFoundException;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class ModuleActionExecutor implements ActionExecutor
{
    protected OrchestratorHandlerContract $orchestratorHandler;
    protected Router $router;
    protected LoggerInterface $logger;

    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler,
        Router $router,
        LoggerInterface $logger
    ) {
        $this->orchestratorHandler = $orchestratorHandler;
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
     * @throws RouteNotFoundException
     */
    protected function validateRoute(string $route): void
    {
        $routes = $this->router->getRoutes();
        $slugs = [];
        /** @var Route $item */
        foreach ($routes as $item) {
            $method = $item->methods();
            if ($method[0] === 'GET') {
                $slugs[] = $item->uri();
            }
        }
        $slugs = array_unique($slugs);

        try {
            Assertion::inArray($route, $slugs);
        } catch (AssertionFailedException $exception) {
            $this->logger->warning('Route not found - Route: '.$route, $exception->getTrace());

            throw new RouteNotFoundException('Route <'.$route.'> not found', Response::HTTP_NOT_FOUND);
        }
    }
}
