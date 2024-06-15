<?php

namespace Tests\Feature\App\Http\Controllers\ActionExecutors\ModuleActions;

use App\Http\Controllers\ActionExecutors\ModuleActions\UpdateModuleActionExecutor;
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

#[CoversClass(UpdateModuleActionExecutor::class)]
class UpdateModuleActionExecutorTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestratorHandler;
    private Router|MockObject $router;
    private LoggerInterface|MockObject $logger;
    private UpdateModuleActionExecutor $actionExecutor;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestratorHandler = $this->createMock(OrchestratorHandlerContract::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->router = $this->createMock(Router::class);
        $this->actionExecutor = new UpdateModuleActionExecutor(
            $this->orchestratorHandler,
            $this->router,
            $this->logger
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestratorHandler,
            $this->actionExecutor,
            $this->router,
            $this->logger
        );
        parent::tearDown();
    }

    /**
     * @throws RouteNotFoundException
     * @throws Exception
     */
    public function test_invoke_should_return_Module(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(5))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                'localhost',
                'name',
                'icon',
                'position',
                'key'
            );

        $requestMock->expects(self::once())
            ->method('merge')
            ->withAnyParameters()
            ->willReturnSelf();

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
            ->with('update-module', $requestMock)
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
            ->willReturn('localhost2');

        $requestMock->expects(self::never())
            ->method('merge');

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

        $this->logger->expects(self::once())
            ->method('warning')
            ->with('Route not found - Route: localhost2');

        $this->orchestratorHandler->expects(self::never())
            ->method('handler');

        $this->expectException(RouteNotFoundException::class);

        $this->actionExecutor->invoke($requestMock);
    }

    public function test_canExecute_should_return_string(): void
    {
        $result = $this->actionExecutor->canExecute();

        $this->assertIsString($result);
        $this->assertSame('update-module-action', $result);
    }
}
