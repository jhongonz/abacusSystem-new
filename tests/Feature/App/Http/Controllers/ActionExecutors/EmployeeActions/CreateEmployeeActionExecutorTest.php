<?php

namespace Tests\Feature\App\Http\Controllers\ActionExecutors\EmployeeActions;

use App\Http\Controllers\ActionExecutors\EmployeeActions\CreateEmployeeActionExecutor;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeImage;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateEmployeeActionExecutor::class)]
class CreateEmployeeActionExecutorTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestratorHandler;
    private CreateEmployeeActionExecutor $actionExecutor;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestratorHandler = $this->createMock(OrchestratorHandlerContract::class);
        $this->actionExecutor = new CreateEmployeeActionExecutor($this->orchestratorHandler);
    }

    public function tearDown(): void
    {
        unset(
            $this->actionExecutor,
            $this->orchestratorHandler
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_invoke_should_return_employee(): void
    {
        $requestMock = $this->createMock(Request::class);
        $employeeMock = $this->createMock(Employee::class);

        $imageMock = $this->createMock(EmployeeImage::class);
        $imageMock->expects(self::once())
            ->method('value')
            ->willReturn('image.jpg');
        $employeeMock->expects(self::once())
            ->method('image')
            ->willReturn($imageMock);

        $employeeIdMock = $this->createMock(EmployeeId::class);
        $employeeIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $employeeMock->expects(self::once())
            ->method('id')
            ->willReturn($employeeIdMock);

        $employeeUserIdMock = $this->createMock(EmployeeUserId::class);
        $employeeUserIdMock->expects(self::once())
            ->method('setValue')
            ->with(1)
            ->willReturnSelf();
        $employeeMock->expects(self::once())
            ->method('userId')
            ->willReturn($employeeUserIdMock);

        $userMock = $this->createMock(User::class);

        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $userMock->expects(self::once())
            ->method('id')
            ->willReturn($userIdMock);

        $this->orchestratorHandler->expects(self::exactly(2))
            ->method('handler')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls($employeeMock, $userMock);

        $result = $this->actionExecutor->invoke($requestMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($employeeMock, $result);
    }

    public function test_canExecute_should_return_string(): void
    {
        $result = $this->actionExecutor->canExecute();

        $this->assertIsString($result);
        $this->assertSame('create-employee-action', $result);
    }
}
