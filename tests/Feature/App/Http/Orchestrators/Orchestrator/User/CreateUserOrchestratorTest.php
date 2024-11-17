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
    public function testMakeShouldCreateAndReturnUser(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(3))
            ->method('input')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(
                1,
                'login',
                'image'
            );

        $requestMock->expects(self::once())
            ->method('integer')
            ->with('employeeId')
            ->willReturn(1);

        $requestMock->expects(self::once())
            ->method('string')
            ->with('password')
            ->willReturn('password');

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

        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertSame($userMock, $result['user']);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('create-user', $result);
    }
}
