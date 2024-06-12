<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Employee;

use App\Http\Orchestrators\Orchestrator\Employee\DeleteEmployeeOrchestrator;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteEmployeeOrchestrator::class)]
class DeleteEmployeeOrchestratorTest extends TestCase
{
    private EmployeeManagementContract|MockObject $employeeManagement;
    private DeleteEmployeeOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employeeManagement = $this->createMock(EmployeeManagementContract::class);
        $this->orchestrator = new DeleteEmployeeOrchestrator($this->employeeManagement);
    }

    public function tearDown(): void
    {
        unset($this->employeeManagement, $this->orchestrator);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_make_should_create_and_return_true(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->willReturn(1);

        $this->employeeManagement->expects(self::once())
            ->method('deleteEmployee')
            ->with(1);

        $result = $this->orchestrator->make($requestMock);
        $this->assertTrue($result);
    }

    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('delete-employee', $result);
    }
}
