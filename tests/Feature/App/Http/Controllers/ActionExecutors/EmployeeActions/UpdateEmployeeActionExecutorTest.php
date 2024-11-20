<?php

namespace Tests\Feature\App\Http\Controllers\ActionExecutors\EmployeeActions;

use App\Http\Controllers\ActionExecutors\EmployeeActions\EmployeeActionExecutor;
use App\Http\Controllers\ActionExecutors\EmployeeActions\UpdateEmployeeActionExecutor;
use App\Http\Orchestrators\OrchestratorHandlerContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateEmployeeActionExecutor::class)]
#[CoversClass(EmployeeActionExecutor::class)]
class UpdateEmployeeActionExecutorTest extends TestCase
{
    private OrchestratorHandlerContract|MockObject $orchestratorHandler;
    private ImageManagerInterface|MockObject $imageManager;
    private Hasher|MockObject $hasher;
    private UpdateEmployeeActionExecutor $actionExecutor;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->orchestratorHandler = $this->createMock(OrchestratorHandlerContract::class);
        $this->hasher = $this->createMock(Hasher::class);
        $this->imageManager = $this->createMock(ImageManagerInterface::class);
        $this->actionExecutor = new UpdateEmployeeActionExecutor(
            $this->orchestratorHandler,
            $this->imageManager,
            $this->hasher
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestratorHandler,
            $this->imageManager,
            $this->hasher,
            $this->actionExecutor
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testInvokeShouldReturnEmployee(): void
    {
        $requestMock = $this->createMock(Request::class);
        $carbonMock = $this->createMock(Carbon::class);

        $requestMock->expects(self::once())
            ->method('date')
            ->with('birthdate', 'd/m/Y')
            ->willReturn($carbonMock);

        $requestMock->expects(self::exactly(10))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                'identifier',
                'typeDocument',
                'name',
                'lastname',
                'email',
                'phone',
                'address',
                'observations',
                'profile',
                'login'
            );

        $requestMock->expects(self::exactly(2))
            ->method('string')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls('token', 'password');

        $requestMock->expects(self::exactly(3))
            ->method('filled')
            ->withAnyParameters()
            ->willReturn(true);

        $requestMock->expects(self::exactly(2))
            ->method('merge')
            ->withAnyParameters()
            ->willReturnSelf();

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

        $this->hasher->expects(self::once())
            ->method('make')
            ->with('password')
            ->willReturn('password');

        $employeeUserIdMock = $this->createMock(EmployeeUserId::class);
        $employeeUserIdMock->expects(self::once())
            ->method('setValue')
            ->with(1)
            ->willReturnSelf();

        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->expects(self::once())
            ->method('userId')
            ->willReturn($employeeUserIdMock);

        $userIdMock = $this->createMock(UserId::class);
        $userIdMock->expects(self::exactly(2))
            ->method('value')
            ->willReturn(1);

        $userMock = $this->createMock(User::class);
        $userMock->expects(self::exactly(2))
            ->method('id')
            ->willReturn($userIdMock);

        $this->orchestratorHandler->expects(self::exactly(2))
            ->method('handler')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                ['employee' => $employeeMock],
                ['user' => $userMock]
            );

        $result = $this->actionExecutor->invoke($requestMock);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($employeeMock, $result);
    }

    public function testCanExecuteShouldReturnString(): void
    {
        $result = $this->actionExecutor->canExecute();

        $this->assertIsString($result);
        $this->assertSame('update-employee-action', $result);
    }
}
