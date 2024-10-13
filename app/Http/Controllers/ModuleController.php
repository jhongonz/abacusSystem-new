<?php

namespace App\Http\Controllers;

use App\Events\Profile\ModuleUpdatedOrDeletedEvent;
use App\Http\Controllers\ActionExecutors\ActionExecutorHandler;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Module\StoreModuleRequest;
use Core\Profile\Domain\Module;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class ModuleController extends Controller implements HasMiddleware
{
    public function __construct(
        private readonly OrchestratorHandlerContract $orchestrators,
        private readonly ActionExecutorHandler $actionExecutorHandler,
        ViewFactory $viewFactory,
        LoggerInterface $logger,
    ) {
        parent::__construct($logger, $viewFactory);
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
        return $this->orchestrators->handler('retrieve-modules', $request);
    }

    public function changeStateModule(Request $request): JsonResponse
    {
        try {
            /** @var Module $module */
            $module = $this->orchestrators->handler('change-state-module', $request);

            ModuleUpdatedOrDeletedEvent::dispatch($module->id()->value());
        } catch (Exception $exception) {
            $this->logger->error('Module can not be updated with id: '.$request->input('moduleId'), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_OK);
    }

    public function getModule(Request $request, ?int $id = null): JsonResponse
    {
        $request->merge(['moduleId' => $id]);
        $dataModule = $this->orchestrators->handler('detail-module', $request);

        $view = $this->viewFactory->make('module.module-form', $dataModule)
            ->render();

        return $this->renderView($view);
    }

    public function storeModule(StoreModuleRequest $request): JsonResponse
    {
        try {
            $method = (! $request->filled('moduleId')) ? 'create-module-action' : 'update-module-action';

            /** @var Module $module */
            $module = $this->actionExecutorHandler->invoke($method, $request);

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
            $request->merge(['moduleId' => $id]);
            $this->orchestrators->handler('delete-module', $request);

            ModuleUpdatedOrDeletedEvent::dispatch($id);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status:Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_OK);
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
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
