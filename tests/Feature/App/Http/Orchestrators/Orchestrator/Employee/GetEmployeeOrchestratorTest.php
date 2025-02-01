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
    public function testMakeShouldReturnEmployeeWithIdentification(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('filled')
            ->with('identification')
            ->willReturn(true);

        $requestMock->expects(self::once())
            ->method('string')
            ->with('identification')
            ->willReturn('test');

        $employeeMock = $this->createMock(Employee::class);
        $this->employeeManagement->expects(self::once())
            ->method('searchEmployeeByIdentification')
            ->with('test')
            ->willReturn($employeeMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('employee', $result);
        $this->assertInstanceOf(Employee::class, $result['employee']);
        $this->assertSame($employeeMock, $result['employee']);
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnEmployeeWithId(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('filled')
            ->with('identification')
            ->willReturn(false);

        $requestMock->expects(self::once())
            ->method('integer')
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

        $this->assertIsArray($result);
        $this->assertArrayHasKey('employee', $result);
        $this->assertInstanceOf(Employee::class, $result['employee']);
        $this->assertSame($employeeMock, $result['employee']);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-employee', $result);
    }
}
