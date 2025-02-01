<?php

namespace Tests\Feature\App\Events\User;

use App\Events\User\RefreshModulesSessionEvent;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Core\Profile\Domain\ValueObjects\ProfileId;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Session\Session;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(RefreshModulesSessionEvent::class)]
class RefreshModulesSessionEventTest extends TestCase
{
    private ProfileManagementContract|MockObject $profileManagementMock;
    private Session|MockObject $sessionMock;
    private RefreshModulesSessionEvent $event;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profileManagementMock = $this->createMock(ProfileManagementContract::class);
        $this->sessionMock = $this->createMock(Session::class);

        $this->event = new RefreshModulesSessionEvent(
            $this->profileManagementMock,
            $this->sessionMock
        );
    }

    public function tearDown(): void
    {
        unset($this->event, $this->profileManagementMock, $this->sessionMock);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testProfileShouldReturnDomainProfile(): void
    {
        $profileId = $this->createMock(ProfileId::class);
        $profileId->expects(self::once())
            ->method('value')
            ->willReturn(1);

        $profileMock = $this->createMock(Profile::class);
        $profileMock->expects(self::once())
            ->method('id')
            ->willReturn($profileId);

        $this->sessionMock->expects($this->once())
            ->method('get')
            ->with('profile')
            ->willReturn($profileMock);

        $profileUpdatedMock = $this->createMock(Profile::class);
        $this->profileManagementMock->expects(self::once())
            ->method('searchProfileById')
            ->with(1)
            ->willReturn($profileUpdatedMock);

        $result = $this->event->profile();

        $this->assertInstanceOf(Profile::class, $result);
        $this->assertSame($profileUpdatedMock, $result);
    }

    public function testBroadcastOnShouldReturnArrayObject(): void
    {
        $result = $this->event->broadcastOn();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertContainsOnlyInstancesOf(PrivateChannel::class, $result);
    }
}
