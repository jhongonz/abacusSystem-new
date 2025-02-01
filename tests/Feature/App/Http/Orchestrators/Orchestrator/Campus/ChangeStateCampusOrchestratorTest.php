<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Campus;

use App\Http\Orchestrators\Orchestrator\Campus\CampusOrchestrator;
use App\Http\Orchestrators\Orchestrator\Campus\ChangeStateCampusOrchestrator;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Core\Campus\Domain\ValueObjects\CampusState;
use Core\Campus\Exceptions\CampusNotFoundException;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(ChangeStateCampusOrchestrator::class)]
#[CoversClass(CampusOrchestrator::class)]
class ChangeStateCampusOrchestratorTest extends TestCase
{
    private CampusManagementContract|MockObject $campusManagementMock;
    private ChangeStateCampusOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->campusManagementMock = $this->createMock(CampusManagementContract::class);
        $this->orchestrator = new ChangeStateCampusOrchestrator($this->campusManagementMock);
    }

    protected function tearDown(): void
    {
        unset(
            $this->campusManagementMock,
            $this->orchestrator
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws CampusNotFoundException
     */
    public function testMakeShouldActiveWhenIsNewAndReturnCampus(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->with('campusId')
            ->willReturn(1);

        $stateMock = $this->createMock(CampusState::class);
        $stateMock->expects(self::once())
            ->method('isNew')
            ->willReturn(true);

        $stateMock->expects(self::once())
            ->method('activate')
            ->willReturnSelf();

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(2);

        $campusMock = $this->createMock(Campus::class);
        $campusMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->campusManagementMock->expects(self::once())
            ->method('searchCampusById')
            ->with(1)
            ->willReturn($campusMock);

        $this->campusManagementMock->expects(self::once())
            ->method('updateCampus')
            ->with(1, ['state' => 2])
            ->willReturn($campusMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('campus', $result);
        $this->assertInstanceOf(Campus::class, $result['campus']);
        $this->assertSame($campusMock, $result['campus']);
    }

    /**
     * @throws Exception
     * @throws CampusNotFoundException
     */
    public function testMakeShouldInactiveWhenIsActiveAndReturnCampus(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->with('campusId')
            ->willReturn(1);

        $stateMock = $this->createMock(CampusState::class);
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
            ->willReturn(3);

        $campusMock = $this->createMock(Campus::class);
        $campusMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->campusManagementMock->expects(self::once())
            ->method('searchCampusById')
            ->with(1)
            ->willReturn($campusMock);

        $this->campusManagementMock->expects(self::once())
            ->method('updateCampus')
            ->with(1, ['state' => 3])
            ->willReturn($campusMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('campus', $result);
        $this->assertInstanceOf(Campus::class, $result['campus']);
        $this->assertSame($campusMock, $result['campus']);
    }

    /**
     * @throws CampusNotFoundException
     * @throws Exception
     */
    public function testMakeShouldReturnException(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->with('campusId')
            ->willReturn(1);

        $this->campusManagementMock->expects(self::once())
            ->method('searchCampusById')
            ->with(1)
            ->willReturn(null);

        $this->expectException(CampusNotFoundException::class);
        $this->expectExceptionMessage('Campus not found with id 1');

        $this->orchestrator->make($requestMock);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('change-state-campus', $result);
    }
}
