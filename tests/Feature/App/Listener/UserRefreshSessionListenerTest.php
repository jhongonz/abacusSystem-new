<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-15 21:11:25
 */

namespace Tests\Feature\App\Listener;

use App\Events\User\RefreshModulesSessionEvent;
use App\Listeners\UserRefreshSessionListener;
use Core\Profile\Domain\Profile;
use Illuminate\Contracts\Session\Session;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserRefreshSessionListener::class)]
class UserRefreshSessionListenerTest extends TestCase
{
    private Session|MockObject $session;
    private UserRefreshSessionListener $listener;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->session = $this->createMock(Session::class);
        $this->listener = new UserRefreshSessionListener($this->session);
    }

    protected function tearDown(): void
    {
        unset($this->session, $this->listener);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldRefreshProfileSession(): void
    {
        $profileMock = $this->createMock(Profile::class);

        $eventMock = $this->createMock(RefreshModulesSessionEvent::class);
        $eventMock->expects(self::once())
            ->method('profile')
            ->willReturn($profileMock);

        $this->session->expects(self::once())
            ->method('forget')
            ->with('profile');

        $this->session->expects(self::once())
            ->method('put')
            ->with('profile', $profileMock);

        $this->listener->handle($eventMock);
    }
}
