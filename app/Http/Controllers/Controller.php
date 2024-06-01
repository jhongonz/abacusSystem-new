<?php

namespace App\Http\Controllers;

use Core\SharedContext\Model\ValueObjectStatus;
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
    protected LoggerInterface $logger;
    protected ViewFactory $viewFactory;


    public function __construct(
        LoggerInterface $logger,
        ViewFactory $viewFactory,
    ) {
        $this->logger = $logger;
        $this->viewFactory = $viewFactory;
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
        $tool = '<div class="btn-group">
        <button type="button" class="btn btn-sm btn-icon rounded-round text-grey-800" data-toggle="dropdown">
            <i class="fas fa-ellipsis-h fa-fw"></i>
        </button>

        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right p-0 ml-4">';

        if (true) {
            $tool .= '<a class="editElement dropdown-item" data-id='.$item['id'].'><i class="fas fa-edit text-primary-600 fa-fw"></i>Editar</a>';

            if ($item['state'] == ValueObjectStatus::STATE_ACTIVE) {
                $tool .= '<a class="changeState dropdown-item" data-id='.$item['id'].'><i class="fas fa-ban text-danger-600 fa-fw"></i>Suspender</a>';
            } elseif (in_array($item['state'], [ValueObjectStatus::STATE_INACTIVE, ValueObjectStatus::STATE_NEW])) {
                $tool .= '<a class="changeState dropdown-item" data-id='.$item['id'].'><i class="fas fa-check text-green-600 fa-fw"></i>Activar</a>';
            }
        }

        if (true) {
            $tool .= '<div class="dropdown-divider m-0 p-0"></div>';
            $tool .= '<a class="deleteElement dropdown-item" data-id='.$item['id'].'><i class="fas fa-eraser text-danger-600 fa-fw"></i>Eliminar</a>';
        }

        $tool .= '</div>
        </div>';

        return $tool;
    }
}
