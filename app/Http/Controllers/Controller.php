<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected function renderView(string $html, int $code = Response::HTTP_OK): JsonResponse|string
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

    protected function getPagination(?string $route = null): string|false
    {
        if (is_null($route)) {
            /** @var Router $routerService */
            $routerService = app(Router::class);

            $routeCurrent = $routerService->current();
            $route = ($routeCurrent) ? $routeCurrent->uri() : '';
        }

        /** @var non-empty-string|false $dataResponse */
        $dataResponse = json_encode([
            'start' => 0,
            'filters' => [],
            'uri' => $route,
        ]);

        return $dataResponse;
    }
}
