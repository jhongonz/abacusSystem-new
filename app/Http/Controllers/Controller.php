<?php

namespace App\Http\Controllers;

use App\Traits\UtilsDateTimeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @codeCoverageIgnore
 */
abstract class Controller
{
    use UtilsDateTimeTrait;

    public function __construct(
        protected readonly LoggerInterface $logger,
        protected readonly ViewFactory $viewFactory,
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
            $route = $routerService->current()->uri();
        }

        return json_encode([
            'start' => 0,
            'filters' => [],
            'uri' => $route
        ]);
    }

    protected function retrieveMenuOptionHtml(array $item, ?string $permission = null): string
    {
        return $this->viewFactory->make('components.menu-options-datatable')
            ->with('item', $item)
            ->with('permission', $permission)
            ->render();
    }
}
