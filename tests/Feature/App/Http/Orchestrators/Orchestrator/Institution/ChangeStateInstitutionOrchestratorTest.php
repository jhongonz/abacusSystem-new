<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Institution;

use App\Http\Orchestrators\Orchestrator\Institution\ChangeStateInstitutionOrchestrator;
use App\Http\Orchestrators\Orchestrator\Institution\InstitutionOrchestrator;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Institution\Domain\Institution;
use Core\Institution\Domain\ValueObjects\InstitutionState;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(ChangeStateInstitutionOrchestrator::class)]
#[CoversClass(InstitutionOrchestrator::class)]
class ChangeStateInstitutionOrchestratorTest extends TestCase
{
    private InstitutionManagementContract|MockObject $institutionManagement;
    private ChangeStateInstitutionOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->institutionManagement = $this->createMock(InstitutionManagementContract::class);
        $this->orchestrator = new ChangeStateInstitutionOrchestrator($this->institutionManagement);
    }

    public function tearDown(): void
    {
        unset(
            $this->institutionManagement,
            $this->orchestrator
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_make_should_return_institution_when_is_activate(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('institutionId')
            ->willReturn(1);

        $institutionMock = $this->createMock(Institution::class);

        $stateMock = $this->createMock(InstitutionState::class);
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

        $institutionMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->institutionManagement->expects(self::once())
            ->method('searchInstitutionById')
            ->with(1)
            ->willReturn($institutionMock);

        $this->institutionManagement->expects(self::once())
            ->method('updateInstitution')
            ->withAnyParameters()
            ->willReturn($institutionMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }

    /**
     * @throws Exception
     */
    public function test_make_should_return_institution_when_is_inactivate(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('institutionId')
            ->willReturn(1);

        $institutionMock = $this->createMock(Institution::class);

        $stateMock = $this->createMock(InstitutionState::class);
        $stateMock->expects(self::once())
            ->method('isNew')
            ->willReturn(false);

        $stateMock->expects(self::once())
            ->method('isInactivated')
            ->willReturn(false);

        $stateMock->expects(self::once())
            ->method('isActivated')
            ->willReturn(true);

        $stateMock->expects(self::never())
            ->method('activate');

        $stateMock->expects(self::once())
            ->method('inactive')
            ->willReturnSelf();

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $institutionMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->institutionManagement->expects(self::once())
            ->method('searchInstitutionById')
            ->with(1)
            ->willReturn($institutionMock);

        $this->institutionManagement->expects(self::once())
            ->method('updateInstitution')
            ->withAnyParameters()
            ->willReturn($institutionMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Institution::class, $result);
        $this->assertSame($institutionMock, $result);
    }

    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('change-state-institution', $result);
    }
}
