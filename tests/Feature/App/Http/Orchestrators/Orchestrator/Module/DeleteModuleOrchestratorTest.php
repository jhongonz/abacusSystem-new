<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Module;

use App\Http\Orchestrators\Orchestrator\Module\DeleteModuleOrchestrator;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteModuleOrchestrator::class)]
class DeleteModuleOrchestratorTest extends TestCase
{
    private ModuleManagementContract|MockObject $moduleManagement;
    private DeleteModuleOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleManagement = $this->createMock(ModuleManagementContract::class);
        $this->orchestrator = new DeleteModuleOrchestrator($this->moduleManagement);
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
    public function testMakeShouldRemoveAndReturnTrue(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->willReturn(1);

        $this->moduleManagement->expects(self::once())
            ->method('deleteModule')
            ->with(1);

        $result = $this->orchestrator->make($requestMock);
        $this->assertTrue($result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('delete-module', $result);
    }
}
