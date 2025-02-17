<?php

namespace App\Http\Controllers;

use App\Events\EventDispatcher;
use App\Events\Profile\ModuleUpdatedOrDeletedEvent;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Module\StoreModuleRequest;
use App\Traits\DataTablesTrait;
use Core\Profile\Domain\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class ModuleController extends Controller implements HasMiddleware
{
    use DataTablesTrait;

    public function __construct(
        private readonly OrchestratorHandlerContract $orchestrators,
        private readonly DataTables $dataTables,
        private readonly EventDispatcher $eventDispatcher,
        protected ViewFactory $viewFactory,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function index(): JsonResponse|string
    {
        $view = $this->viewFactory->make('module.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    /**
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    public function getModules(Request $request): JsonResponse
    {
        $dataModules = $this->orchestrators->handler('retrieve-modules', $request);

        $datatable = $this->dataTables->collection($dataModules);
        $datatable->addColumn('tools', function (array $item) {
            return $this->retrieveMenuOptionHtml($item);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    public function changeStateModule(Request $request): JsonResponse
    {
        try {
            /** @var array{module: Module} $dataModule */
            $dataModule = $this->orchestrators->handler('change-state-module', $request);
            $module = $dataModule['module'];

            /** @var int $moduleId */
            $moduleId = $module->id()->value();
            $this->eventDispatcher->dispatch(new ModuleUpdatedOrDeletedEvent($moduleId));

        } catch (\Exception $exception) {
            /** @var string $moduleId */
            $moduleId = $request->input('moduleId');

            $this->logger->error(
                sprintf('Module can not be updated with id: %s', $moduleId),
                $exception->getTrace()
            );

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(status: Response::HTTP_OK);
    }

    public function getModule(Request $request, ?int $id = null): JsonResponse|string
    {
        $request->merge(['moduleId' => $id]);

        /** @var array<int|string, mixed> $dataModule */
        $dataModule = $this->orchestrators->handler('detail-module', $request);

        $view = $this->viewFactory->make('module.module-form', $dataModule)
            ->render();

        return $this->renderView($view);
    }

    public function storeModule(StoreModuleRequest $request): JsonResponse
    {
        try {
            $method = (!$request->filled('moduleId')) ? 'create-module' : 'update-module';

            /** @var array{module: Module} $dataModule */
            $dataModule = $this->orchestrators->handler($method, $request);
            $module = $dataModule['module'];

            /** @var int $moduleId */
            $moduleId = $module->id()->value();
            $this->eventDispatcher->dispatch(new ModuleUpdatedOrDeletedEvent($moduleId));
        } catch (\Exception $exception) {
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

            $this->eventDispatcher->dispatch(new ModuleUpdatedOrDeletedEvent($id));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return new JsonResponse(status: Response::HTTP_INTERNAL_SERVER_ERROR);
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
