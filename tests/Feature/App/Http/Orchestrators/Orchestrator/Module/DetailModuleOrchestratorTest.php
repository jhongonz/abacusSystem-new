<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Module;

use App\Http\Orchestrators\Orchestrator\Module\DetailModuleOrchestrator;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Illuminate\Config\Repository as Config;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DetailModuleOrchestrator::class)]
class DetailModuleOrchestratorTest extends TestCase
{
    private ModuleManagementContract|MockObject $moduleManagement;
    private Config|MockObject $config;
    private DetailModuleOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleManagement = $this->createMock(ModuleManagementContract::class);
        $this->config = $this->createMock(Config::class);
        $this->orchestrator = new DetailModuleOrchestrator(
            $this->moduleManagement,
            $this->config
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->moduleManagement,
            $this->config
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnArrayWithModule(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->with('moduleId')
            ->willReturn(1);

        $moduleMock = $this->createMock(Module::class);
        $this->moduleManagement->expects(self::once())
            ->method('searchModuleById')
            ->with(1)
            ->willReturn($moduleMock);

        $this->config->expects(self::once())
            ->method('get')
            ->with('menu.options')
            ->willReturn(['testing']);

        $result = $this->orchestrator->make($requestMock);

        $dataExpected = [
            'moduleId' => 1,
            'module' => $moduleMock,
            'menuKeys' => ['testing'],
        ];

        $this->assertIsArray($result);
        $this->assertSame($dataExpected, $result);
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnArrayWithoutModule(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->with('moduleId')
            ->willReturn(null);

        $this->moduleManagement->expects(self::once())
            ->method('searchModuleById')
            ->willReturn(null);

        $this->config->expects(self::once())
            ->method('get')
            ->with('menu.options')
            ->willReturn(['testing']);

        $result = $this->orchestrator->make($requestMock);

        $dataExpected = [
            'moduleId' => null,
            'module' => null,
            'menuKeys' => ['testing'],
        ];

        $this->assertIsArray($result);
        $this->assertSame($dataExpected, $result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('detail-module', $result);
    }
}
