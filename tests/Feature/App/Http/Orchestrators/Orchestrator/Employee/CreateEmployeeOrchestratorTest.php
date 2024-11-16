<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Employee;

use App\Http\Orchestrators\Orchestrator\Employee\CreateEmployeeOrchestrator;
use Carbon\Carbon;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Illuminate\Http\Request;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateEmployeeOrchestrator::class)]
class CreateEmployeeOrchestratorTest extends TestCase
{
    private EmployeeManagementContract|MockObject $employeeManagement;
    private ImageManagerInterface|MockObject $imageManager;
    private CreateEmployeeOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employeeManagement = $this->createMock(EmployeeManagementContract::class);
        $this->imageManager = $this->createMock(ImageManagerInterface::class);
        $this->orchestrator = new CreateEmployeeOrchestrator(
            $this->employeeManagement,
            $this->imageManager
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->employeeManagement,
            $this->imageManager
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldCreateAndReturnEmployee(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(11))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                1,
                1,
                'identification',
                'name',
                'lastname',
                'identification_type',
                'observations',
                'email',
                'phone',
                'address',
                'token',
            );

        $requestMock->expects(self::once())
            ->method('filled')
            ->with('token')
            ->willReturn(true);

        $carbonMock = $this->createMock(Carbon::class);
        $carbonMock->expects(self::once())
            ->method('format')
            ->with('Y-m-d')
            ->willReturn('2024-06-11');

        $requestMock->expects(self::once())
            ->method('date')
            ->with('birthdate', 'd/m/Y')
            ->willReturn($carbonMock);

        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects(self::exactly(2))
            ->method('save')
            ->withAnyParameters()
            ->willReturnSelf();

        $imageMock->expects(self::once())
            ->method('resize')
            ->with(150, 150)
            ->willReturnSelf();

        $this->imageManager->expects(self::once())
            ->method('read')
            ->with('/var/www/abacusSystem-new/public/images/tmp/token.jpg')
            ->willReturn($imageMock);

        $employeeMock = $this->createMock(Employee::class);
        $this->employeeManagement->expects(self::once())
            ->method('createEmployee')
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
        $this->assertSame('create-employee', $result);
    }
}
