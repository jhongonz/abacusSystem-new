<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\User;

use App\Http\Orchestrators\Orchestrator\User\GetUserOrchestrator;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(GetUserOrchestrator::class)]
class GetUserOrchestratorTest extends TestCase
{
    private UserManagementContract|MockObject $userManagement;
    private GetUserOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userManagement = $this->createMock(UserManagementContract::class);
        $this->orchestrator = new GetUserOrchestrator($this->userManagement);
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->userManagement
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnUserWhenSearchById(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('filled')
            ->with('login')
            ->willReturn(false);

        $requestMock->expects(self::once())
            ->method('integer')
            ->with('userId')
            ->willReturn(1);

        $userMock = $this->createMock(User::class);
        $this->userManagement->expects(self::once())
            ->method('searchUserById')
            ->with(1)
            ->willReturn($userMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($userMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnUserWhenSearchByLogin(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('filled')
            ->with('login')
            ->willReturn(true);

        $requestMock->expects(self::once())
            ->method('string')
            ->with('login')
            ->willReturn('test');

        $userMock = $this->createMock(User::class);
        $this->userManagement->expects(self::once())
            ->method('searchUserByLogin')
            ->with('test')
            ->willReturn($userMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertSame($userMock, $result['user']);
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnNullWhenSearchById(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('filled')
            ->with('login')
            ->willReturn(false);

        $requestMock->expects(self::once())
            ->method('integer')
            ->with('userId')
            ->willReturn(1);

        $this->userManagement->expects(self::once())
            ->method('searchUserById')
            ->with(1)
            ->willReturn(null);

        $result = $this->orchestrator->make($requestMock);

        $this->assertNotInstanceOf(User::class, $result['user']);
        $this->assertNull($result['user']);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-user', $result);
    }
}
