<?php

namespace App\Http\Controllers;

use App\Events\Profile\ModuleUpdatedOrDeletedEvent;
use App\Events\User\RefreshModulesSession;
use App\Http\Exceptions\RouteNotFoundException;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Module\StoreModuleRequest;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Core\Profile\Domain\Module;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class ModuleController extends Controller implements HasMiddleware
{
    private OrchestratorHandlerContract $orchestratorHandler;

    public function __construct(
        OrchestratorHandlerContract $orchestratorHandler,
        ViewFactory $viewFactory,
        LoggerInterface $logger,
    ) {
        parent::__construct($logger, $viewFactory);

        $this->orchestratorHandler = $orchestratorHandler;
    }

    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('module.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    public function getModules(Request $request): JsonResponse
    {
        return $this->orchestratorHandler->handler('retrieve-modules', $request);
    }

    public function changeStateModule(Request $request): JsonResponse
    {
        try {
            /** @var Module $module */
            $module = $this->orchestratorHandler->handler('change-state-module', $request);

            ModuleUpdatedOrDeletedEvent::dispatch($module->id()->value());
            RefreshModulesSession::dispatch();
        } catch (Exception $exception) {
            $this->logger->error('Module can not be updated with id: '.$request->input('moduleId'), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    public function getModule(Request $request, ?int $id = null): JsonResponse
    {
        $request->mergeIfMissing(['moduleId' => $id]);
        $dataModule = $this->orchestratorHandler->handler('detail-module', $request);

        $view = $this->viewFactory->make('module.module-form', $dataModule);

        return $this->renderView($view);
    }

    public function storeModule(StoreModuleRequest $request): JsonResponse
    {
        try {
            $method = (is_null($request->input('moduleId'))) ? 'createModule' : 'updateModule';

            /** @var Module $module */
            $module = $this->{$method}($request);

            ModuleUpdatedOrDeletedEvent::dispatch($module->id()->value());
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(
                ['msg' => 'Ha ocurrido un error al guardar el registro, consulte con su administrador de sistemas'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(status: Response::HTTP_CREATED);
    }

    public function deleteModule(Request $request, int $id): JsonResponse
    {
        try {
            $request->mergeIfMissing(['moduleId' => $id]);
            $this->orchestratorHandler->handler('delete-module', $request);

            ModuleUpdatedOrDeletedEvent::dispatch($id);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
        }

        return new JsonResponse(status: Response::HTTP_OK);
    }

    /**
     * @throws RouteNotFoundException
     */
    private function createModule(StoreModuleRequest $request): Module
    {
        $this->validateRoute($request->input('route'));

        return $this->orchestratorHandler->handler('create-module', $request);
    }

    /**
     * @throws RouteNotFoundException
     */
    public function updateModule(StoreModuleRequest $request): Module
    {
        $route = $request->input('route');
        $this->validateRoute($route);

        $dataUpdate = [
            'name' => $request->input('name'),
            'route' => $route,
            'icon' => $request->input('icon'),
            'key' => $request->input('key'),
        ];

        $request->mergeIfMissing(['dataUpdate' => json_encode($dataUpdate)]);
        return $this->orchestratorHandler->handler('update-module', $request);
    }

    /**
     * @throws RouteNotFoundException
     */
    private function validateRoute(string $route): void
    {
        $routes = Route::getRoutes();
        $slugs = [];
        /** @var \Illuminate\Routing\Route $item */
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
            throw new RouteNotFoundException('Route not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): Middleware|array
    {
        return [
            new Middleware(['auth', 'verify-session']),
            new Middleware('only.ajax-request', only: [
                'getModules',
                'changeStateModule',
                'deleteModule',
                'getModule',
                'storeModule',
            ]),
        ];
    }
}
