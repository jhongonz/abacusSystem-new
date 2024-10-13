<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Module;

use App\Http\Orchestrators\Orchestrator\Module\ModuleOrchestrator;
use App\Http\Orchestrators\Orchestrator\Module\UpdateModuleOrchestrator;
use App\Traits\RouterTrait;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\Request;
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
    public function test_make_should_update_and_return_module(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(2))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls('{}', 1);

        $moduleMock = $this->createMock(Module::class);
        $this->moduleManagement->expects(self::once())
            ->method('updateModule')
            ->withAnyParameters()
            ->willReturn($moduleMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Module::class, $result);
    }

    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('update-module', $result);
    }
}
