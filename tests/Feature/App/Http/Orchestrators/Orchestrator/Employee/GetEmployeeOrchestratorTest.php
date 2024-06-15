<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Employee;

use App\Http\Orchestrators\Orchestrator\Employee\GetEmployeeOrchestrator;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(GetEmployeeOrchestrator::class)]
class GetEmployeeOrchestratorTest extends TestCase
{
    private EmployeeManagementContract|MockObject $employeeManagement;
    private GetEmployeeOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employeeManagement = $this->createMock(EmployeeManagementContract::class);
        $this->orchestrator = new GetEmployeeOrchestrator($this->employeeManagement);
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->employeeManagement
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_make_should_return_employee_with_identification(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('filled')
            ->with('identification')
            ->willReturn(true);

        $requestMock->expects(self::once())
            ->method('input')
            ->with('identification')
            ->willReturn('test');

        $employeeMock = $this->createMock(Employee::class);
        $this->employeeManagement->expects(self::once())
            ->method('searchEmployeeByIdentification')
            ->with('test')
            ->willReturn($employeeMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($employeeMock, $result);
    }

    /**
     * @throws Exception
     */
    public function test_make_should_return_employee_with_id(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('filled')
            ->with('identification')
            ->willReturn(false);

        $requestMock->expects(self::once())
            ->method('input')
            ->with('employeeId')
            ->willReturn(1);

        $employeeMock = $this->createMock(Employee::class);
        $this->employeeManagement->expects(self::never())
            ->method('searchEmployeeByIdentification');

        $this->employeeManagement->expects(self::once())
            ->method('searchEmployeeById')
            ->with(1)
            ->willReturn($employeeMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($employeeMock, $result);
    }

    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-employee', $result);
    }
}
