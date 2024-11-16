<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    public function __construct(
        protected readonly LoggerInterface $logger,
    ) {
    }

    public function renderView(string $html, int $code = Response::HTTP_OK): JsonResponse|string
    {
        /** @var Request $requestService */
        $requestService = app(Request::class);

        if ($requestService->ajax()) {
            $response = new JsonResponse(['html' => $html], $code);

            return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
        }

        return $html;
    }

    public function getPagination(?string $route = null): string
    {
        if (is_null($route)) {

            /** @var Router $routerService */
            $routerService = app(Router::class);

            $routeCurrent = $routerService->current();
            $route = ($routeCurrent) ? $routeCurrent->uri() : '';
        }

        return (string) json_encode([
            'start' => 0,
            'filters' => [],
            'uri' => $route
        ]);
    }
}
