<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\User;

use App\Http\Orchestrators\Orchestrator\User\ChangeStateUserOrchestrator;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(ChangeStateUserOrchestrator::class)]
class ChangeStateUserOrchestratorTest extends TestCase
{
    private UserManagementContract|MockObject $userManagement;
    private ChangeStateUserOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userManagement = $this->createMock(UserManagementContract::class);
        $this->orchestrator = new ChangeStateUserOrchestrator($this->userManagement);
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
     * @throws \Exception
     */
    public function testMakeShouldReturnUser(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::exactly(2))
            ->method('integer')
            ->willReturnOnConsecutiveCalls(2, 1);

        $userMock = $this->createMock(User::class);
        $this->userManagement->expects(self::once())
            ->method('updateUser')
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
        $this->assertSame('change-state-user', $result);
    }
}
