<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-15 20:52:49
 */

namespace Tests\Feature\App\Listener;

use App\Events\User\UserUpdateOrDeleteEvent;
use App\Jobs\ProcessCommandWarmup;
use App\Listeners\UserWarmupListener;
use App\Listeners\WarmupListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(UserWarmupListener::class)]
#[CoversClass(WarmupListener::class)]
class UserWarmupListenerTest extends TestCase
{
    private UserWarmupListener|MockObject $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->listener = $this->getMockBuilder(UserWarmupListener::class)
            ->onlyMethods(['callCommandWarmup'])
            ->getMock();
    }

    protected function tearDown(): void
    {
        unset($this->listener);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldExecuteEventCommand(): void
    {
        $userId = 1;

        $eventMock = $this->createMock(UserUpdateOrDeleteEvent::class);
        $eventMock->expects(self::once())
            ->method('userId')
            ->willReturn($userId);

        $commandWarmupMock = $this->createMock(ProcessCommandWarmup::class);
        $commandWarmupMock->expects(self::once())
            ->method('handle');

        $this->listener->expects(self::once())
            ->method('callCommandWarmup')
            ->with('user:warmup 1')
            ->willReturn($commandWarmupMock);

        $this->listener->handle($eventMock);
    }
}
