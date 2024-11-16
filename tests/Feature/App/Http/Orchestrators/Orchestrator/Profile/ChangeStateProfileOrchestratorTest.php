<?php

namespace Tests\Feature\App\Http\Orchestrators\Orchestrator\Profile;

use App\Http\Orchestrators\Orchestrator\Profile\ChangeStateProfileOrchestrator;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\ValueObjects\ProfileState;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(ChangeStateProfileOrchestrator::class)]
class ChangeStateProfileOrchestratorTest extends TestCase
{
    private ProfileManagementContract|MockObject $profileManagement;
    private ChangeStateProfileOrchestrator $orchestrator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profileManagement = $this->createMock(ProfileManagementContract::class);
        $this->orchestrator = new ChangeStateProfileOrchestrator($this->profileManagement);
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
    public function testMakeShouldChangeStateWhenIsNewAndReturnProfile(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('profileId')
            ->willReturn(1);

        $stateMock = $this->createMock(ProfileState::class);
        $stateMock->expects(self::once())
            ->method('isNew')
            ->willReturn(true);

        $stateMock->expects(self::never())
            ->method('isInactivated');

        $stateMock->expects(self::once())
            ->method('activate')
            ->willReturnSelf();

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(2);

        $profileMock = $this->createMock(Profile::class);
        $profileMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->profileManagement->expects(self::once())
            ->method('searchProfileById')
            ->with(1)
            ->willReturn($profileMock);

        $this->profileManagement->expects(self::once())
            ->method('updateProfile')
            ->withAnyParameters()
            ->willReturn($profileMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    /**
     * @throws Exception
     */
    public function testMakeShouldChangeStateWhenIsActivatedAndReturnProfile(): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->expects(self::once())
            ->method('input')
            ->with('profileId')
            ->willReturn(1);

        $stateMock = $this->createMock(ProfileState::class);
        $stateMock->expects(self::once())
            ->method('isNew')
            ->willReturn(false);

        $stateMock->expects(self::once())
            ->method('isInactivated')
        ->willReturn(false);

        $stateMock->expects(self::never())
            ->method('activate');

        $stateMock->expects(self::once())
            ->method('isActivated')
            ->willReturn(true);

        $stateMock->expects(self::once())
            ->method('inactive')
            ->willReturnSelf();

        $stateMock->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $profileMock = $this->createMock(Profile::class);
        $profileMock->expects(self::once())
            ->method('state')
            ->willReturn($stateMock);

        $this->profileManagement->expects(self::once())
            ->method('searchProfileById')
            ->with(1)
            ->willReturn($profileMock);

        $this->profileManagement->expects(self::once())
            ->method('updateProfile')
            ->withAnyParameters()
            ->willReturn($profileMock);

        $result = $this->orchestrator->make($requestMock);

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileMock, $result);
    }

    public function testCanOrchestrateShouldReturnString(): void
    {
        $result = $this->orchestrator->canOrchestrate();

        $this->assertIsString($result);
        $this->assertSame('change-state-profile', $result);
    }
}
