<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Employee;

use App\Http\Orchestrators\Orchestrator\Employee\DetailEmployeeOrchestrator;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use Core\Institution\Domain\Contracts\InstitutionManagementContract;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profiles;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DetailEmployeeOrchestrator::class)]
class DetailEmployeeOrchestratorTest extends TestCase
{
    private UserManagementContract|MockObject $userManagement;
    private ProfileManagementContract|MockObject $profileManagement;
    private EmployeeManagementContract|MockObject $employeeManagement;
    private InstitutionManagementContract|MockObject $institutionManagement;
    private DetailEmployeeOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userManagement = $this->createMock(UserManagementContract::class);
        $this->profileManagement = $this->createMock(ProfileManagementContract::class);
        $this->employeeManagement = $this->createMock(EmployeeManagementContract::class);
        $this->institutionManagement = $this->createMock(InstitutionManagementContract::class);
        $this->orchestrator = new DetailEmployeeOrchestrator(
            $this->employeeManagement,
            $this->userManagement,
            $this->profileManagement,
            $this->institutionManagement
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->employeeManagement,
            $this->profileManagement,
            $this->userManagement,
            $this->institutionManagement
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_make_should_return_array_with_employee(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('employeeId')
            ->willReturn(1);

        $employeeMock = $this->createMock(Employee::class);

        $employeeUserIdMock = $this->createMock(EmployeeUserId::class);
        $employeeUserIdMock->expects(self::once())
            ->method('value')
            ->willReturn(1);
        $employeeMock->expects(self::once())
            ->method('userId')
            ->willReturn($employeeUserIdMock);

        $this->employeeManagement->expects(self::once())
            ->method('searchEmployeeById')
            ->with(1)
            ->willReturn($employeeMock);

        $userMock = $this->createMock(User::class);
        $this->userManagement->expects(self::once())
            ->method('searchUserById')
            ->with(1)
            ->willReturn($userMock);

        $profilesMock = $this->createMock(Profiles::class);
        $this->profileManagement->expects(self::once())
            ->method('searchProfiles')
            ->willReturn($profilesMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['userId']);
        $this->assertSame(1, $result['employeeId']);
        $this->assertSame($employeeMock, $result['employee']);
        $this->assertSame($userMock, $result['user']);
        $this->assertSame($profilesMock, $result['profiles']);
        $this->assertIsString($result['image']);
    }

    /**
     * @throws Exception
     */
    public function test_make_should_return_array_with_null(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('employeeId')
            ->willReturn(null);

        $this->employeeManagement->expects(self::never())
            ->method('searchEmployeeById');

        $this->userManagement->expects(self::never())
            ->method('searchUserById');

        $profilesMock = $this->createMock(Profiles::class);
        $this->profileManagement->expects(self::once())
            ->method('searchProfiles')
            ->willReturn($profilesMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertNull($result['userId']);
        $this->assertNull($result['employeeId']);
        $this->assertNull($result['employee']);
        $this->assertNull($result['user']);
        $this->assertSame($profilesMock, $result['profiles']);
        $this->assertNull($result['image']);
    }

    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('detail-employee', $result);
    }
}
