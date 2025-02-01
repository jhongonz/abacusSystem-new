<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Module;

use App\Http\Exceptions\RouteNotFoundException;
use App\Http\Orchestrators\Orchestrator\Module\ModuleOrchestrator;
use App\Http\Orchestrators\Orchestrator\Module\UpdateModuleOrchestrator;
use App\Traits\RouterTrait;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Routing\Router;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

#[CoversClass(UpdateModuleOrchestrator::class)]
#[CoversClass(ModuleOrchestrator::class)]
#[CoversClass(RouterTrait::class)]
class UpdateModuleOrchestratorTest extends TestCase
{
    private ModuleManagementContract|MockObject $moduleManagement;
    private Router|MockObject $routerMock;
    private LoggerInterface|MockObject $loggerMock;
    private UpdateModuleOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleManagement = $this->createMock(ModuleManagementContract::class);
        $this->routerMock = $this->createMock(Router::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->orchestrator = new UpdateModuleOrchestrator(
            $this->moduleManagement,
            $this->routerMock,
            $this->loggerMock
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->moduleManagement
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldUpdateAndReturnModule(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(4))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                'name',
                'icon',
                'position',
                'key'
            );

        $requestMock->expects(self::once())
            ->method('string')
            ->with('route')
            ->willReturn('localhost');

        $requestMock->expects(self::once())
            ->method('integer')
            ->with('moduleId')
            ->willReturn(1);

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('methods')
            ->willReturn(['GET']);

        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('localhost');

        $routeCollectionMock = $this->createMock(RouteCollectionInterface::class);
        $routeCollectionMock->expects(self::once())
            ->method('getRoutes')
            ->willReturn([$routeMock]);

        $this->routerMock->expects(self::once())
            ->method('getRoutes')
            ->willReturn($routeCollectionMock);

        $dataExpected = [
            'name' => 'name',
            'route' => 'localhost',
            'icon' => 'icon',
            'position' => 'position',
            'key' => 'key',
        ];

        $moduleMock = $this->createMock(Module::class);
        $this->moduleManagement->expects(self::once())
            ->method('updateModule')
            ->with(1, $dataExpected)
            ->willReturn($moduleMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('module', $result);
        $this->assertInstanceOf(Module::class, $result['module']);
        $this->assertSame($moduleMock, $result['module']);
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldUpdateAndReturnException(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('string')
            ->with('route')
            ->willReturn('localhost');

        $routeMock = $this->createMock(Route::class);
        $routeMock->expects(self::once())
            ->method('methods')
            ->willReturn(['GET']);

        $routeMock->expects(self::once())
            ->method('uri')
            ->willReturn('testing');

        $routeCollectionMock = $this->createMock(RouteCollectionInterface::class);
        $routeCollectionMock->expects(self::once())
            ->method('getRoutes')
            ->willReturn([$routeMock]);

        $this->routerMock->expects(self::once())
            ->method('getRoutes')
            ->willReturn($routeCollectionMock);

        $this->moduleManagement->expects(self::never())
            ->method('updateModule');

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
        $this->assertSame('update-module', $result);
    }
}
