<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Module;

use App\Http\Exceptions\RouteNotFoundException;
use App\Http\Orchestrators\Orchestrator\Module\CreateModuleOrchestrator;
use App\Http\Orchestrators\Orchestrator\Module\ModuleOrchestrator;
use App\Traits\RouterTrait;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(CreateModuleOrchestrator::class)]
#[CoversClass(ModuleOrchestrator::class)]
#[CoversClass(RouterTrait::class)]
class CreateModuleOrchestratorTest extends TestCase
{
    private ModuleManagementContract|MockObject $moduleManagement;
    private Router|MockObject $routerMock;
    private LoggerInterface|MockObject $loggerMock;
    private CreateModuleOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleManagement = $this->createMock(ModuleManagementContract::class);
        $this->routerMock = $this->createMock(Router::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->orchestrator = new CreateModuleOrchestrator(
            $this->moduleManagement,
            $this->routerMock,
            $this->loggerMock
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->moduleManagement,
            $this->loggerMock,
            $this->routerMock
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldCreateAndReturnModule(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(5))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                'localhost',
                'key',
                'name',
                'icon',
                'position'
            );

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('methods')
            ->willReturn(['GET']);

        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('localhost');

        $this->routerMock->expects(self::once())
            ->method('getRoutes')
            ->willReturn([$routeMock]);

        $moduleMock = $this->createMock(Module::class);
        $this->moduleManagement->expects(self::once())
            ->method('createModule')
            ->withAnyParameters()
            ->willReturn($moduleMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldCreateAndReturnException(): void
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
            ->willReturn('testing');

        $this->routerMock->expects(self::once())
            ->method('getRoutes')
            ->willReturn([$routeMock]);

        $this->moduleManagement->expects(self::never())
            ->method('createModule');

        $this->loggerMock->expects(self::once())
            ->method('error')
            ->with('Value "localhost" is not an element of the valid values: testing');

        $this->expectException(RouteNotFoundException::class);
        $this->expectExceptionMessage('Route <localhost> not found');

        $this->orchestrator->make($requestMock);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('create-module', $result);
    }
}
