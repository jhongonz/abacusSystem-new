<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Employee;

use App\Http\Orchestrators\Orchestrator\Employee\GetEmployeesOrchestrator;
use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(GetEmployeesOrchestrator::class)]
class GetEmployeesOrchestratorTest extends TestCase
{
    private EmployeeDataTransformerContract|MockObject $employeeDataTransformer;
    private EmployeeManagementContract|MockObject $employeeManagement;
    private GetEmployeesOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employeeDataTransformer = $this->createMock(EmployeeDataTransformerContract::class);
        $this->employeeManagement = $this->createMock(EmployeeManagementContract::class);

        $this->orchestrator = new GetEmployeesOrchestrator(
            $this->employeeManagement,
            $this->employeeDataTransformer
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->employeeManagement,
            $this->employeeDataTransformer,
            $this->orchestrator,
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnArray(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('filters')
            ->willReturn([]);

        $employeesMock = new Employees();
        $employeeMock = $this->createMock(Employee::class);
        $employeesMock->addItem($employeeMock);

        $this->employeeManagement->expects(self::once())
            ->method('searchEmployees')
            ->with([])
            ->willReturn($employeesMock);

        $this->employeeDataTransformer->expects(self::once())
            ->method('write')
            ->with($employeeMock)
            ->willReturnSelf();

        $this->employeeDataTransformer->expects(self::once())
            ->method('readToShare')
            ->willReturn([]);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertSame([[]], $result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-employees', $result);
    }
}
