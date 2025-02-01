<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-15 18:59:30
 */

namespace Tests\Feature\App\Listener;

use App\Events\Campus\CampusUpdatedOrDeletedEvent;
use App\Jobs\ProcessCommandWarmup;
use App\Listeners\CampusWarmupListener;
use App\Listeners\WarmupListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(CampusWarmupListener::class)]
#[CoversClass(WarmupListener::class)]
class CampusWarmupListenerTest extends TestCase
{
    private CampusWarmupListener|MockObject $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->listener = $this->getMockBuilder(CampusWarmupListener::class)
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
        $campusId = 1;

        $eventMock = $this->createMock(CampusUpdatedOrDeletedEvent::class);
        $eventMock->expects(self::once())
            ->method('campusId')
            ->willReturn($campusId);

        $commandWarmupMock = $this->createMock(ProcessCommandWarmup::class);
        $commandWarmupMock->expects(self::once())
            ->method('handle');

        $this->listener->expects(self::once())
            ->method('callCommandWarmup')
            ->with('campus:warmup 1')
            ->willReturn($commandWarmupMock);

        $this->listener->handle($eventMock);
    }
}
