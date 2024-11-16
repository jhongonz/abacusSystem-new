<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Employee;

use App\Http\Orchestrators\Orchestrator\Employee\ChangeStateEmployeeOrchestrator;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(ChangeStateEmployeeOrchestrator::class)]
class ChangeStateEmployeeOrchestratorTest extends TestCase
{
    private EmployeeManagementContract|MockObject $employeeManagement;
    private ChangeStateEmployeeOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employeeManagement = $this->createMock(EmployeeManagementContract::class);
        $this->orchestrator = new ChangeStateEmployeeOrchestrator($this->employeeManagement);
    }

    public function tearDown(): void
    {
        unset($this->orchestrator, $this->employeeManagement);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldActiveWhenIsNewAndReturnEmployee(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('id')
            ->willReturn(1);

        $employeeMock = $this->createMock(Employee::class);
        $stateMock = $this->createMock(EmployeeState::class);
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

        $employeeMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->employeeManagement->expects(self::once())
            ->method('searchEmployeeById')
            ->with(1)
            ->willReturn($employeeMock);

        $this->employeeManagement->expects(self::once())
            ->method('updateEmployee')
            ->withAnyParameters()
            ->willReturn($employeeMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($employeeMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldActiveWhenIsIsActivatedAndReturnEmployee(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('id')
            ->willReturn(1);

        $employeeMock = $this->createMock(Employee::class);
        $stateMock = $this->createMock(EmployeeState::class);
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
            ->willReturn(3);

        $employeeMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->employeeManagement->expects(self::once())
            ->method('searchEmployeeById')
            ->with(1)
            ->willReturn($employeeMock);

        $this->employeeManagement->expects(self::once())
            ->method('updateEmployee')
            ->withAnyParameters()
            ->willReturn($employeeMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($employeeMock, $result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('change-state-employee', $result);
    }
}
