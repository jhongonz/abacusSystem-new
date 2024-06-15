<?php

namespace Tests\Feature\App\Http\Controllers\ActionExecutors\ModuleActions;

use App\Http\Controllers\ActionExecutors\ModuleActions\CreateModuleActionExecutor;
use App\Http\Exceptions\RouteNotFoundException;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(CreateModuleActionExecutor::class)]
class CreateModuleActionExecutorTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestratorHandler;
    private Router|MockObject $router;
    private LoggerInterface|MockObject $logger;
    private CreateModuleActionExecutor $actionExecutor;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestratorHandler = $this->createMock(OrchestratorHandlerContract::class);
        $this->router = $this->createMock(Router::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->actionExecutor = new CreateModuleActionExecutor(
            $this->orchestratorHandler,
            $this->router,
            $this->logger
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestratorHandler,
            $this->router,
            $this->logger,
            $this->actionExecutor
        );
        parent::tearDown();
    }

    /**
     * @throws RouteNotFoundException
     * @throws Exception
     */
    public function test_invoke_should_return_module(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('route')
            ->willReturn('localhost');

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

        $moduleMock = $this->createMock(Module::class);
        $this->orchestratorHandler->expects(self::once())
            ->method('handler')
            ->with('create-module', $requestMock)
            ->willReturn($moduleMock);

        $result = $this->actionExecutor->invoke($requestMock);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    /**
     * @throws RouteNotFoundException
     * @throws Exception
     */
    public function test_invoke_should_return_exception(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('route')
            ->willReturn('localhost');

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('methods')
            ->willReturn(['GET']);

        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('localhost2');

        $this->router->expects(self::once())
            ->method('getRoutes')
            ->willReturn([$routeMock]);

        $this->orchestratorHandler->expects(self::never())
            ->method('handler');

        $this->logger->expects(self::once())
            ->method('warning')
            ->with('Route not found - Route: localhost');

        $this->expectException(RouteNotFoundException::class);

        $this->actionExecutor->invoke($requestMock);
    }

    public function test_canExecute_should_return_string(): void
    {
        $result = $this->actionExecutor->canExecute();

        $this->assertIsString($result);
        $this->assertSame('create-module-action', $result);
    }
}
