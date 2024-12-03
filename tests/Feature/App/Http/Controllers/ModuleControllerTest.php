<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Events\EventDispatcher;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ModuleController;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use App\Http\Requests\Module\StoreModuleRequest;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\ValueObjects\ModuleId;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\View\Factory as ViewFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\DataTables;

#[CoversClass(ModuleController::class)]
#[CoversClass(Controller::class)]
class ModuleControllerTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestrator;
    private DataTables|MockObject $dataTables;
    private ViewFactory|MockObject $viewFactory;
    private LoggerInterface|MockObject $logger;
    private EventDispatcher|MockObject $eventDispatcher;
    private ModuleController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestrator = $this->createMock(OrchestratorHandlerContract::class);
        $this->dataTables = $this->createMock(DataTables::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);

        $this->controller = new ModuleController(
            $this->orchestrator,
            $this->dataTables,
            $this->eventDispatcher,
            $this->viewFactory,
            $this->logger,
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->viewFactory,
            $this->logger,
            $this->controller,
            $this->actionExecutorHandler,
            $this->eventDispatcher
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testIndexShouldReturnJsonResponse(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(true);
        $this->app->instance(Request::class, $request);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('modules');

        $routerMock = $this->createMock(Router::class);
        $routerMock->expects(self::once())
            ->method('current')
            ->willReturn($routeMock);
        $this->app->instance(Router::class, $routerMock);

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('with')
            ->with('pagination', '{"start":0,"filters":[],"uri":"modules"}')
            ->willReturnSelf();

        $html = '<html lang="es"></html>';
        $view->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('module.index')
            ->willReturn($view);

        $result = $this->controller->index();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame(['html' => $html], $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function testIndexShouldReturnString(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects(self::once())
            ->method('ajax')
            ->willReturn(false);
        $this->app->instance(Request::class, $request);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('modules');

        $routerMock = $this->createMock(Router::class);
        $routerMock->expects(self::once())
            ->method('current')
            ->willReturn($routeMock);
        $this->app->instance(Router::class, $routerMock);

        $view = $this->createMock(View::class);
        $view->expects(self::once())
            ->method('with')
            ->with('pagination', '{"start":0,"filters":[],"uri":"modules"}')
            ->willReturnSelf();

        $html = '<html lang="es"></html>';
        $view->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('module.index')
            ->willReturn($view);

        $result = $this->controller->index();

        $this->assertNotInstanceOf(JsonResponse::class, $result);
        $this->assertSame($html, $result);
    }

    /**
     * @throws Exception
     * @throws \Yajra\DataTables\Exceptions\Exception
     */
    public function testGetModulesShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('retrieve-modules', $requestMock)
            ->willReturn([]);

        $collectionDataTableMock = $this->createMock(CollectionDataTable::class);
        $collectionDataTableMock->expects(self::once())
            ->method('addColumn')
            ->with('tools', $this->callback(function ($closure) {
                $viewMock = $this->createMock(View::class);
                $viewMock->expects(self::exactly(2))
                    ->method('with')
                    ->withAnyParameters()
                    ->willReturnSelf();

                $viewMock->expects(self::once())
                    ->method('render')
                    ->willReturn('<html lang="es"></html>');

                $this->viewFactory->expects(self::once())
                    ->method('make')
                    ->with('components.menu-options-datatable')
                    ->willReturn($viewMock);

                $view = $closure(['id' => 1, 'state' => 2]);

                $this->assertIsString($view);
                $this->assertSame('<html lang="es"></html>', $view);

                return true;
            }))
            ->willReturnSelf();

        $collectionDataTableMock->expects(self::once())
            ->method('escapeColumns')
            ->with([])
            ->willReturnSelf();

        $responseMock = $this->createMock(JsonResponse::class);
        $collectionDataTableMock->expects(self::once())
            ->method('toJson')
            ->willReturn($responseMock);

        $this->dataTables->expects(self::once())
            ->method('collection')
            ->with([])
            ->willReturn($collectionDataTableMock);

        $result = $this->controller->getModules($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame($responseMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testChangeStateModuleShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);
        $moduleMock = $this->createMock(Module::class);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $moduleMock->expects(self::once())
            ->method('id')
            ->willReturn($moduleIdMock);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('change-state-module', $requestMock)
            ->willReturn(['module' => $moduleMock]);

        $result = $this->controller->changeStateModule($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testChangeStateModuleShouldReturnJsonResponseWhenIsException(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('moduleId')
            ->willReturn(1);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('change-state-module', $requestMock)
            ->willThrowException(new \Exception('Can not update state of module'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Module can not be updated with id: 1');

        $result = $this->controller->changeStateModule($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testGetModuleShouldReturnJsonResponseWhenIdIsNull(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['moduleId' => null])
            ->willReturnSelf();

        $requestMock->expects(self::once())
            ->method('ajax')
            ->willReturn(true);
        $this->app->instance(Request::class, $requestMock);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('detail-module', $requestMock)
            ->willreturn([]);

        $viewMock = $this->createMock(View::class);

        $html = '<html lang="es"></html>';
        $viewMock->expects(self::once())
            ->method('render')
            ->willReturn($html);

        $this->viewFactory->expects(self::once())
            ->method('make')
            ->with('module.module-form', [])
            ->willReturn($viewMock);

        $result = $this->controller->getModule($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame(['html' => $html], $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function testStoreModuleShouldCreateNewModuleAndReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(StoreModuleRequest::class);
        $requestMock->expects(self::once())
            ->method('filled')
            ->with('moduleId')
            ->willReturn(false);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $moduleMock = $this->createMock(Module::class);
        $moduleMock->expects(self::once())
            ->method('id')
            ->willReturn($moduleIdMock);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('create-module', $requestMock)
            ->willReturn(['module' => $moduleMock]);

        $result = $this->controller->storeModule($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testStoreModuleShouldReturnJsonResponseWhenExceptionRouting(): void
    {
        $requestMock = $this->createMock(StoreModuleRequest::class);
        $requestMock->expects(self::once())
            ->method('filled')
            ->with('moduleId')
            ->willReturn(false);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('create-module', $requestMock)
            ->willThrowException(new \Exception('Can not create module'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not create module');

        $result = $this->controller->storeModule($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
        $this->assertArrayHasKey('msg', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function testStoreModuleShouldUpdateModuleAndReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(StoreModuleRequest::class);
        $requestMock->expects(self::once())
            ->method('filled')
            ->with('moduleId')
            ->willReturn(true);

        $moduleIdMock = $this->createMock(ModuleId::class);
        $moduleIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $moduleMock = $this->createMock(Module::class);
        $moduleMock->expects(self::once())
            ->method('id')
            ->willReturn($moduleIdMock);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('update-module', $requestMock)
            ->willReturn(['module' => $moduleMock]);

        $result = $this->controller->storeModule($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testStoreModuleShouldReturnJsonResponseWhenIsException(): void
    {
        $requestMock = $this->createMock(StoreModuleRequest::class);
        $requestMock->expects(self::once())
            ->method('filled')
            ->with('moduleId')
            ->willReturn(true);

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('update-module', $requestMock)
            ->willThrowException(new \Exception('Can not update module'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not update module');

        $result = $this->controller->storeModule($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
        $this->assertArrayHasKey('msg', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function testDeleteModuleShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['moduleId' => 1])
            ->willReturnSelf();

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('delete-module', $requestMock)
            ->willReturn([]);

        $result = $this->controller->deleteModule($requestMock, 1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function testDeleteModuleShouldReturnJsonResponseWhenIsException(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('merge')
            ->with(['moduleId' => 1])
            ->willReturnSelf();

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('delete-module', $requestMock)
            ->willThrowException(new \Exception('Can not delete module'));

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Can not delete module');

        $result = $this->controller->deleteModule($requestMock, 1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
    }

    public function testMiddlewareShouldReturnObject(): void
    {
        $result = $this->controller::middleware();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Middleware::class, $result);
    }
}
