<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Campus;

use App\Http\Orchestrators\Orchestrator\Campus\CampusOrchestrator;
use App\Http\Orchestrators\Orchestrator\Campus\ChangeStateCampusOrchestrator;
use Core\Campus\Domain\Campus;
use Core\Campus\Domain\Contracts\CampusManagementContract;
use Core\Campus\Domain\ValueObjects\CampusState;
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
     * @return void
     * @throws Exception
     */
    public function test_make_should_active_when_is_new_and_return_campus(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
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

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_make_should_inactive_when_is_active_and_return_campus(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
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

        $this->assertInstanceOf(Campus::class, $result);
        $this->assertSame($campusMock, $result);
    }

    /**
     * @return void
     */
    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('change-state-campus', $result);
    }
}
