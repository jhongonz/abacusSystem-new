<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Module;

use App\Http\Orchestrators\Orchestrator\Module\GetModulesOrchestrator;
use Core\Profile\Domain\Contracts\ModuleDataTransformerContract;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(GetModulesOrchestrator::class)]
class GetModulesOrchestratorTest extends TestCase
{
    private ModuleManagementContract|MockObject $moduleManagement;
    private ModuleDataTransformerContract|MockObject $moduleDataTransformer;
    private GetModulesOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleManagement = $this->createMock(ModuleManagementContract::class);
        $this->moduleDataTransformer = $this->createMock(ModuleDataTransformerContract::class);
        $this->orchestrator = new GetModulesOrchestrator(
            $this->moduleManagement,
            $this->moduleDataTransformer,
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->moduleManagement,
            $this->orchestrator,
            $this->moduleDataTransformer,
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnJsonResponse(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('filters')
            ->willReturn([]);

        $moduleMock = $this->createMock(Module::class);
        $moduleMock2 = $this->createMock(Module::class);
        $modulesMock = new Modules([$moduleMock, $moduleMock2]);

        $this->moduleManagement->expects(self::once())
            ->method('searchModules')
            ->with([])
            ->willReturn($modulesMock);

        $this->moduleDataTransformer->expects(self::exactly(2))
            ->method('write')
            ->with($moduleMock)
            ->willReturnSelf();

        $this->moduleDataTransformer->expects(self::exactly(2))
            ->method('readToShare')
            ->willReturnOnConsecutiveCalls(['sandbox' => 'testing'], ['sandbox2' => 'testing']);

        $result = $this->orchestrator->make($requestMock);

        $dataExpected = [['sandbox' => 'testing'], ['sandbox2' => 'testing']];
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals($dataExpected, $result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-modules', $result);
    }
}
