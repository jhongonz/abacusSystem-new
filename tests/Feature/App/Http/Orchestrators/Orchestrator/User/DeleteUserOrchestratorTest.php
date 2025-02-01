<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\User;

use App\Http\Orchestrators\Orchestrator\User\DeleteUserOrchestrator;
use Core\User\Domain\Contracts\UserManagementContract;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteUserOrchestrator::class)]
class DeleteUserOrchestratorTest extends TestCase
{
    private UserManagementContract|MockObject $userManagement;
    private DeleteUserOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->userManagement = $this->createMock(UserManagementContract::class);
        $this->orchestrator = new DeleteUserOrchestrator($this->userManagement);
    }

    public function tearDown(): void
    {
        unset(
            $this->userManagement,
            $this->orchestrator
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldDeleteAndReturnTrue(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('integer')
            ->with('userId')
            ->willReturn(1);

        $this->userManagement->expects(self::once())
            ->method('deleteUser')
            ->with(1);

        $result = $this->orchestrator->make($requestMock);
        $this->assertIsArray($result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('delete-user', $result);
    }
}
