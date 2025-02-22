<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2025-02-19 07:47:53
 */

namespace Feature\App\Listener;

use App\Events\Institution\InstitutionUpdateOrDeletedEvent;
use App\Jobs\ProcessCommandWarmup;
use App\Listeners\InstitutionWarmupListener;
use App\Listeners\WarmupListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(InstitutionWarmupListener::class)]
#[CoversClass(WarmupListener::class)]
class InstitutionWarmupListenerTest extends TestCase
{
    private InstitutionWarmupListener|MockObject $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->listener = $this->getMockBuilder(InstitutionWarmupListener::class)
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
     * @throws Exception
     */
    public function testHandleShouldExecuteEventCommand(): void
    {
        $institutionId = 1;

        $eventMock = $this->createMock(InstitutionUpdateOrDeletedEvent::class);
        $eventMock->expects(self::once())
            ->method('institutionId')
            ->willReturn($institutionId);

        $commandWarmupMock = $this->createMock(ProcessCommandWarmup::class);
        $commandWarmupMock->expects(self::once())
            ->method('handle');

        $this->listener->expects(self::once())
            ->method('callCommandWarmup')
            ->with('institution:warmup 1')
            ->willReturn($commandWarmupMock);

        $this->listener->handle($eventMock);
    }
}
