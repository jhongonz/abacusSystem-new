<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Profile;

use App\Http\Orchestrators\Orchestrator\Profile\GetProfileOrchestrator;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(GetProfileOrchestrator::class)]
class GetProfileOrchestratorTest extends TestCase
{
    private ProfileManagementContract|MockObject $profileManagement;
    private GetProfileOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profileManagement = $this->createMock(ProfileManagementContract::class);
        $this->orchestrator = new GetProfileOrchestrator($this->profileManagement);
    }

    public function tearDown(): void
    {
        unset(
            $this->orchestrator,
            $this->profileManagement
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnProfile(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('profileId')
            ->willReturn(1);

        $profileMock = $this->createMock(Profile::class);
        $this->profileManagement->expects(self::once())
            ->method('searchProfileById')
            ->with(1)
            ->willReturn($profileMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldReturnNull(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('profileId')
            ->willReturn(1);

        $this->profileManagement->expects(self::once())
            ->method('searchProfileById')
            ->with(1)
            ->willReturn(null);

        $result = $this->orchestrator->make($requestMock);

        $this->assertNotInstanceOf(Profile::class, $result);
        $this->assertNull($result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('retrieve-profile', $result);
    }
}
