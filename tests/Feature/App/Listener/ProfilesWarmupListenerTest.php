<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-15 20:12:39
 */

namespace Tests\Feature\App\Listener;

use App\Events\Profile\ModuleUpdatedOrDeletedEvent;
use App\Events\Profile\ProfileUpdatedOrDeletedEvent;
use App\Jobs\ProcessCommandWarmup;
use App\Listeners\ProfilesWarmupListener;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProfilesWarmupListenerTest extends TestCase
{
    private ProfilesWarmupListener|MockObject $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->listener = $this->getMockBuilder(ProfilesWarmupListener::class)
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
    public function testHandleShouldExecuteEventCommandWithProfileEvent(): void
    {
        $eventMock = $this->createMock(ProfileUpdatedOrDeletedEvent::class);

        $commandWarmupMock = $this->createMock(ProcessCommandWarmup::class);
        $this->listener->expects($this->once())
            ->method('callCommandWarmup')
            ->with(['profile:warmup', 'module:warmup'])
            ->willReturn($commandWarmupMock);

        $this->listener->handle($eventMock);
    }

    /**
     * @throws Exception
     */
    public function testHandleShouldExecuteEventCommandWithModuleEvent(): void
    {
        $eventMock = $this->createMock(ModuleUpdatedOrDeletedEvent::class);

        $commandWarmupMock = $this->createMock(ProcessCommandWarmup::class);
        $commandWarmupMock->expects($this->once())
            ->method('handle');

        $this->listener->expects($this->once())
            ->method('callCommandWarmup')
            ->with(['profile:warmup', 'module:warmup'])
            ->willReturn($commandWarmupMock);

        $this->listener->handle($eventMock);
    }
}
