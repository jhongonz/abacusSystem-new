<?php

namespace Tests\Feature\App\Http\Controllers;

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

#[CoversClass(ModuleController::class)]
class ModuleControllerTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestrator;
    private ViewFactory|MockObject $viewFactory;
    private LoggerInterface|MockObject $logger;
    private Router|MockObject $router;
    private ModuleController $controller;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestrator = $this->createMock(OrchestratorHandlerContract::class);
        $this->viewFactory = $this->createMock(ViewFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->router = $this->createMock(Router::class);

        $this->controller = new ModuleController(
            $this->orchestrator,
            $this->viewFactory,
            $this->logger,
            $this->router
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->viewFactory,
            $this->logger,
            $this->controller,
            $this->router
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_index_should_return_json_response(): void
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
    public function test_index_should_return_string(): void
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
     */
    public function test_getModules_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);

        $responseMock = $this->createMock(JsonResponse::class);
        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('retrieve-modules', $requestMock)
            ->willReturn($responseMock);

        $result = $this->controller->getModules($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame($responseMock, $result);
    }

    /**
     * @throws Exception
     */
    public function test_changeStateModule_should_return_json_response(): void
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
            ->willReturn($moduleMock);

        $result = $this->controller->changeStateModule($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_changeStateModule_should_return_json_response_when_is_exception(): void
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
    public function test_getModule_should_return_json_response_when_id_is_null(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('mergeIfMissing')
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
    public function test_storeModule_should_create_new_module_and_return_json_response(): void
    {
        $requestMock = $this->createMock(StoreModuleRequest::class);
        $requestMock->expects(self::exactly(2))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(null, 'testing');

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('methods')
            ->willReturn(['GET']);

        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('testing');

        $this->router->expects(self::once())
            ->method('getRoutes')
            ->willReturn([$routeMock]);

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
            ->willReturn($moduleMock);

        $result = $this->controller->storeModule($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_storeModule_should_return_json_response_when_exception_routing(): void
    {
        $requestMock = $this->createMock(StoreModuleRequest::class);
        $requestMock->expects(self::exactly(2))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(null, 'testing');

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('methods')
            ->willReturn(['GET']);

        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('localhost');

        $this->router->expects(self::once())
            ->method('getRoutes')
            ->willReturn([$routeMock]);

        $this->orchestrator->expects(self::never())
            ->method('handler');

        $this->logger->expects(self::once())
            ->method('warning')
            ->with('Route not found - Route: testing');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Route <testing> not found');

        $result = $this->controller->storeModule($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
        $this->assertArrayHasKey('msg', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_storeModule_should_update_module_and_return_json_response(): void
    {
        $requestMock = $this->createMock(StoreModuleRequest::class);
        $requestMock->expects(self::exactly(5))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                1,
                'testing',
                'name',
                'icon',
                'key'
            );

        $requestMock->expects(self::once())
            ->method('mergeIfMissing')
            ->withAnyParameters()
            ->willReturnSelf();

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('methods')
            ->willReturn(['GET']);

        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('testing');

        $this->router->expects(self::once())
            ->method('getRoutes')
            ->willReturn([$routeMock]);

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
            ->willReturn($moduleMock);

        $result = $this->controller->storeModule($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(201, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_storeModule_should_return_json_response_when_is_exception(): void
    {
        $requestMock = $this->createMock(StoreModuleRequest::class);
        $requestMock->expects(self::exactly(2))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                1,
                'testing',
            );

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('methods')
            ->willReturn(['GET']);

        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('localhost');

        $this->router->expects(self::once())
            ->method('getRoutes')
            ->willReturn([$routeMock]);

        $this->orchestrator->expects(self::never())
            ->method('handler');

        $this->logger->expects(self::once())
            ->method('warning')
            ->with('Route not found - Route: testing');

        $this->logger->expects(self::once())
            ->method('error')
            ->with('Route <testing> not found');

        $result = $this->controller->storeModule($requestMock);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(500, $result->getStatusCode());
        $this->assertArrayHasKey('msg', $result->getData(true));
    }

    /**
     * @throws Exception
     */
    public function test_deleteModule_should_return_json_response(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('mergeIfMissing')
            ->with(['moduleId' => 1])
            ->willReturnSelf();

        $this->orchestrator->expects(self::once())
            ->method('handler')
            ->with('delete-module', $requestMock)
            ->willReturn(true);

        $result = $this->controller->deleteModule($requestMock, 1);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @throws Exception
     */
    public function test_deleteModule_should_return_json_response_when_is_exception(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('mergeIfMissing')
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

    public function test_middleware_should_return_object(): void
    {
        $result = $this->controller::middleware();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        foreach ($result as $item) {
            $this->assertInstanceOf(Middleware::class, $item);
        }
    }
}
