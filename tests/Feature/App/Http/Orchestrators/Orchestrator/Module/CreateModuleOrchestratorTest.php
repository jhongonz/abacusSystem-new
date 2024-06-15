<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Module;

use App\Http\Orchestrators\Orchestrator\Module\CreateModuleOrchestrator;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateModuleOrchestrator::class)]
class CreateModuleOrchestratorTest extends TestCase
{
    private ModuleManagementContract|MockObject $moduleManagement;
    private CreateModuleOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleManagement = $this->createMock(ModuleManagementContract::class);
        $this->orchestrator = new CreateModuleOrchestrator($this->moduleManagement);
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
    public function test_make_should_create_and_return_module(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(5))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                'key',
                'name',
                'route',
                'icon',
                'position'
            );

        $moduleMock = $this->createMock(Module::class);
        $this->moduleManagement->expects(self::once())
            ->method('createModule')
            ->withAnyParameters()
            ->willReturn($moduleMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Module::class, $result);
        $this->assertSame($moduleMock, $result);
    }

    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('create-module', $result);
    }
}
