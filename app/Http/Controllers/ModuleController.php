<?php

namespace App\Http\Controllers;

use App\Events\Profile\ModuleUpdatedOrDeletedEvent;
use App\Events\User\RefreshModulesSession;
use App\Http\Exceptions\RouteNotFoundException;
use App\Http\Requests\Module\StoreModuleRequest;
use Core\Profile\Domain\Contracts\ModuleDataTransformerContract;
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Route;
use JetBrains\PhpStorm\NoReturn;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class ModuleController extends Controller implements HasMiddleware
{
    private ModuleFactoryContract $moduleFactory;
    private ModuleManagementContract $moduleService;
    private ModuleDataTransformerContract $moduleDataTransformer;
    private DataTables $dataTable;

    public function __construct(
        ModuleFactoryContract $moduleFactory,
        ModuleManagementContract $moduleService,
        ModuleDataTransformerContract $moduleDataTransformer,
        DataTables $dataTable,
        LoggerInterface $logger,
    ){
        parent::__construct($logger);

        $this->moduleFactory = $moduleFactory;
        $this->moduleService = $moduleService;
        $this->moduleDataTransformer = $moduleDataTransformer;
        $this->dataTable = $dataTable;
    }

    public function index(): JsonResponse|string
    {
        $view = view('module.index')
            ->with('pagination', $this->getPagination())
            ->render();

        return $this->renderView($view);
    }

    /**
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    public function getModules(Request $request): JsonResponse
    {
        $modules = $this->moduleService->searchModules($request->filters);
        return $this->prepareListModules($modules);
    }

    public function changeStateModule(Request $request): JsonResponse
    {
        $moduleId = $this->moduleFactory->buildModuleId($request->id);
        $module = $this->moduleService->searchModuleById($moduleId);

        if ($module->state()->isNew() || $module->state()->isInactived()) {
            $module->state()->activate();
        } else if ($module->state()->isActived()) {
            $module->state()->inactive();
        }

        $dataUpdate['state'] = $module->state()->value();

        try {
            $this->moduleService->updateModule($moduleId, $dataUpdate);
            ModuleUpdatedOrDeletedEvent::dispatch($moduleId);
            RefreshModulesSession::dispatch();
        } catch (Exception $exception) {
            $this->logger->error('Module can not be updated with id: '. $moduleId->value());

            return response()->json(status:Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(status:Response::HTTP_CREATED);
    }

    public function getModule(null|int $id = null): JsonResponse
    {
        $module = null;
        if (!is_null($id)) {
            $moduleId = $this->moduleFactory->buildModuleId($id);
            $module = $this->moduleService->searchModuleById($moduleId);
        }

        $view = view('module.module-form')
            ->with('id', $id)
            ->with('module', $module)
            ->with('menuKeys', config('menu.options'));

        return $this->renderView($view);
    }

    public function storeModule(StoreModuleRequest $request): JsonResponse
    {
        $moduleId = $this->moduleFactory->buildModuleId($request->id);

        try {
            $method = (is_null($moduleId->value())) ? 'createModule' : 'updateModule';
            $this->{$method}($request, $moduleId);
            ModuleUpdatedOrDeletedEvent::dispatch($moduleId);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            return response()->json(['msg'=>'Ha ocurrido un error al guardar el registro, consulte con su administrador de sistemas'],
            Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(status:Response::HTTP_CREATED);
    }

    public function deleteModule(int $id): JsonResponse
    {
        $moduleId = $this->moduleFactory->buildModuleId($id);
        $this->moduleService->deleteModule($moduleId);

        ModuleUpdatedOrDeletedEvent::dispatch($moduleId);

        return response()->json(status:Response::HTTP_OK);
    }

    /**
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    private function prepareListModules(Modules $modules): JsonResponse
    {
        $dataModules = [];
        if ($modules->count()) {
            /** @var Module $item */
            foreach ($modules as $item) {
                $dataModules[] = $this->moduleDataTransformer->write($item)->readToShare();
            }
        }

        $datatable = $this->dataTable->collection(collect($dataModules));
        $datatable->addColumn('tools', function(array $item) {
            return $this->retrieveMenuOptionHtml($item);
        });

        return $datatable->escapeColumns([])->toJson();
    }

    /**
     * @throws RouteNotFoundException
     */
    #[NoReturn] private function createModule(StoreModuleRequest $request, ModuleId $id): void
    {
        $this->validateRoute($request->route);

        $module = $this->moduleFactory->buildModule(
            $id,
            $this->moduleFactory->buildModuleMenuKey($request->key),
            $this->moduleFactory->buildModuleName($request->name),
            $this->moduleFactory->buildModuleRoute($request->route),
            $this->moduleFactory->buildModuleIcon($request->icon),
        );

        $this->moduleService->createModule($module);
    }

    /**
     * @throws RouteNotFoundException
     */
    #[NoReturn] public function updateModule(StoreModuleRequest $request, ModuleId $id): void
    {
        $this->validateRoute($request->route);

        $dataUpdate = [
            'name' => $request->name,
            'route' => $request->route,
            'icon' => $request->icon,
            'key' => $request->key,
        ];

        $this->moduleService->updateModule($id, $dataUpdate);
    }

    /**
     * @throws RouteNotFoundException
     */
    private function validateRoute(string $route): void
    {
        $routes = Route::getRoutes();
        $slugs = [];
        /**@var \Illuminate\Routing\Route $item */
        foreach ($routes as $item) {
            $method = $item->methods();
            if ($method[0] === 'GET') {
                $slugs[] = $item->uri();
            }
        }
        $slugs = array_unique($slugs);

        if (!in_array($route, $slugs, true)) {
            $this->logger->info('Route not found - Route: '.$route);
            throw new RouteNotFoundException('Route not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return Middleware|array
     */
    public static function middleware(): Middleware|array
    {
        return [
            new Middleware('auth'),
            new Middleware('only.ajax-request', only:[
                'getModules',
                'changeStateModule',
                'deleteModule',
                'getModule',
                'storeModule',
            ]),
        ];
    }
}
