<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Module;

use App\Http\Orchestrators\Orchestrator\Module\ChangeStateModuleOrchestrator;
use Core\Profile\Domain\Contracts\ModuleManagementContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\ValueObjects\ModuleState;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(ChangeStateModuleOrchestrator::class)]
class ChangeStateModuleOrchestratorTest extends TestCase
{
    private ModuleManagementContract|MockObject $moduleManagement;
    private ChangeStateModuleOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->moduleManagement = $this->createMock(ModuleManagementContract::class);
        $this->orchestrator = new ChangeStateModuleOrchestrator($this->moduleManagement);
    }

    public function tearDown(): void
    {
        unset(
            $this->moduleManagement,
            $this->orchestrator
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Core\Profile\Exceptions\ModuleNotFoundException
     */
    public function testMakeShouldReturnModuleWhenIsActivate(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->with('moduleId')
            ->willReturn(1);

        $moduleMock = $this->createMock(Module::class);
        $stateMock = $this->createMock(ModuleState::class);

        $stateMock->expects(self::once())
            ->method('isNew')
            ->willReturn(true);

        $stateMock->expects(self::never())
            ->method('isInactivated');

        $stateMock->expects(self::once())
            ->method('activate')
            ->willReturnSelf();

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(2);

        $moduleMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->moduleManagement->expects(self::once())
            ->method('searchModuleById')
            ->with(1)
            ->willReturn($moduleMock);

        $this->moduleManagement->expects(self::once())
            ->method('updateModule')
            ->withAnyParameters()
            ->willReturn($moduleMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('module', $result);
        $this->assertInstanceOf(Module::class, $result['module']);
        $this->assertSame($moduleMock, $result['module']);
    }

    /**
     * @throws Exception
     * @throws \Core\Profile\Exceptions\ModuleNotFoundException
     */
    public function testMakeShouldReturnModuleWhenIsInactivate(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->with('moduleId')
            ->willReturn(1);

        $moduleMock = $this->createMock(Module::class);
        $stateMock = $this->createMock(ModuleState::class);

        $stateMock->expects(self::once())
            ->method('isNew')
            ->willReturn(false);

        $stateMock->expects(self::once())
            ->method('isInactivated')
        ->willReturn(false);

        $stateMock->expects(self::never())
            ->method('activate');

        $stateMock->expects(self::once())
            ->method('isActivated')
            ->willReturn(true);

        $stateMock->expects(self::once())
            ->method('inactive')
            ->willReturnSelf();

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $moduleMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->moduleManagement->expects(self::once())
            ->method('searchModuleById')
            ->with(1)
            ->willReturn($moduleMock);

        $this->moduleManagement->expects(self::once())
            ->method('updateModule')
            ->withAnyParameters()
            ->willReturn($moduleMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('module', $result);
        $this->assertInstanceOf(Module::class, $result['module']);
        $this->assertSame($moduleMock, $result['module']);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('change-state-module', $result);
    }
}
