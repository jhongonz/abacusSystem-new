<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Profile;

use App\Http\Orchestrators\Orchestrator\Profile\DeleteProfileOrchestrator;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(DeleteProfileOrchestrator::class)]
class DeleteProfileOrchestratorTest extends TestCase
{
    private ProfileManagementContract|MockObject $profileManagement;
    private DeleteProfileOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profileManagement = $this->createMock(ProfileManagementContract::class);
        $this->orchestrator = new DeleteProfileOrchestrator($this->profileManagement);
    }

    public function tearDown(): void
    {
        unset(
            $this->profileManagement,
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
            ->method('input')
            ->with('profileId')
            ->willReturn(1);

        $this->profileManagement->expects(self::once())
            ->method('deleteProfile')
            ->with(1);

        $result = $this->orchestrator->make($requestMock);
        $this->assertTrue($result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('delete-profile', $result);
    }
}
