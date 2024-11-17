<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Employee;

use App\Http\Orchestrators\Orchestrator\Employee\UpdateEmployeeOrchestrator;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateEmployeeOrchestrator::class)]
class UpdateEmployeeOrchestratorTest extends TestCase
{
    private EmployeeManagementContract|MockObject $employeeManagement;
    private UpdateEmployeeOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employeeManagement = $this->createMock(EmployeeManagementContract::class);
        $this->orchestrator = new UpdateEmployeeOrchestrator($this->employeeManagement);
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
     * @throws \Exception
     */
    public function testMakeShouldReturnEmployee(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('string')
            ->with('dataUpdate')
            ->willReturn('{"birthdate":"2024-06-04 12:34:56"}');

        $requestMock->expects(self::once())
            ->method('integer')
            ->with('employeeId')
            ->willReturn(1);

        $employeeMock = $this->createMock(Employee::class);
        $this->employeeManagement->expects(self::once())
            ->method('updateEmployee')
            ->withAnyParameters()
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
        $this->assertSame('update-employee', $result);
    }
}
