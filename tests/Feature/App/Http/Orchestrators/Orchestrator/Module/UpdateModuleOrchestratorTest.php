<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Module;

use App\Http\Orchestrators\Orchestrator\Module\UpdateModuleOrchestrator;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateModuleOrchestrator::class)]
class UpdateModuleOrchestratorTest extends TestCase
{
    private ModuleManagementContract|MockObject $moduleManagement;
    private UpdateModuleOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleManagement = $this->createMock(ModuleManagementContract::class);
        $this->orchestrator = new UpdateModuleOrchestrator($this->moduleManagement);
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
