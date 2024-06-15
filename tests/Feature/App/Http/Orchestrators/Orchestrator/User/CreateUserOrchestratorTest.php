<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\User;

use App\Http\Orchestrators\Orchestrator\User\CreateUserOrchestrator;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(CreateUserOrchestrator::class)]
class CreateUserOrchestratorTest extends TestCase
{
    private UserManagementContract|MockObject $userManagement;
    private Hasher|MockObject $hasher;
    private CreateUserOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userManagement = $this->createMock(UserManagementContract::class);
        $this->hasher = $this->createMock(Hasher::class);
        $this->orchestrator = new CreateUserOrchestrator(
            $this->userManagement,
            $this->hasher
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->hasher,
            $this->userManagement
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function test_make_should_create_and_return_user(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(5))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                1,
                1,
                'login',
                'password',
                'image'
            );

        $this->hasher->expects(self::once())
            ->method('make')
            ->with('password')
            ->willReturn('password');

        $userMock = $this->createMock(User::class);
        $this->userManagement->expects(self::once())
            ->method('createUser')
            ->withAnyParameters()
            ->willReturn($userMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($userMock, $result);
    }

    public function test_canOrchestrate_should_return_string(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('create-user', $result);
    }
}
