<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\User;

use App\Http\Orchestrators\Orchestrator\User\UpdateUserOrchestrator;
use Core\User\Domain\Contracts\UserManagementContract;
use Core\User\Domain\User;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(UpdateUserOrchestrator::class)]
class UpdateUserOrchestratorTest extends TestCase
{
    private UserManagementContract|MockObject $userManagement;
    private UpdateUserOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userManagement = $this->createMock(UserManagementContract::class);
        $this->orchestrator = new UpdateUserOrchestrator($this->userManagement);
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
    public function testMakeShouldUpdateAndReturnUser(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('string')
            ->with('dataUpdate')
            ->willReturn('{}');

        $requestMock->expects(self::once())
            ->method('integer')
            ->with('userId')
            ->willReturn(1);

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
        $this->assertSame('update-user', $result);
    }
}
