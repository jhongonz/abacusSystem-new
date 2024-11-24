<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Employee;

use App\Http\Orchestrators\Orchestrator\Employee\CreateEmployeeOrchestrator;
use Carbon\Carbon;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
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
     * @throws \Exception
     */
    public function testMakeShouldCreateAndReturnEmployee(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(8))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                'identification',
                'name',
                'lastname',
                'identification_type',
                'observations',
                'email',
                'phone',
                'address'
            );

        $requestMock->expects(self::exactly(2))
            ->method('integer')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(1, 1);

        $requestMock->expects(self::once())
            ->method('string')
            ->with('token')
            ->willReturn('token');

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

        Str::createUuidsUsing(function () {
            return Uuid::fromString('46aa4b5e-615d-466c-ab38-6674ec52637b');
        });

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

        $dataExpected = [
            'id' => 1,
            'userId' => null,
            'institutionId' => 1,
            'identification' => 'identification',
            'name' => 'name',
            'lastname' => 'lastname',
            'identification_type' => 'identification_type',
            'observations' => 'observations',
            'email' => 'email',
            'phone' => 'phone',
            'address' => 'address',
            'state' => 1,
            'birthdate' => '2024-06-11 00:00:00',
            'image' => '46aa4b5e-615d-466c-ab38-6674ec52637b.jpg',
        ];

        $employeeMock = $this->createMock(Employee::class);
        $this->employeeManagement->expects(self::once())
            ->method('createEmployee')
            ->with([Employee::TYPE => $dataExpected])
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
        $this->assertSame('create-employee', $result);
    }
}
