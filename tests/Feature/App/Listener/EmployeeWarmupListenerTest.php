<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-15 19:48:52
 */

namespace Tests\Feature\App\Listener;

use App\Events\Employee\EmployeeUpdateOrDeletedEvent;
use App\Jobs\ProcessCommandWarmup;
use App\Listeners\EmployeeWarmupListener;
use App\Listeners\WarmupListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(EmployeeWarmupListener::class)]
#[CoversClass(WarmupListener::class)]
class EmployeeWarmupListenerTest extends TestCase
{
    private EmployeeWarmupListener|MockObject $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->listener = $this->getMockBuilder(EmployeeWarmupListener::class)
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
        $employeeId = 1;

        $eventMock = $this->createMock(EmployeeUpdateOrDeletedEvent::class);
        $eventMock->expects(self::once())
            ->method('employeeId')
            ->willReturn($employeeId);

        $commandWarmupMock = $this->createMock(ProcessCommandWarmup::class);
        $commandWarmupMock->expects(self::once())
            ->method('handle');

        $this->listener->expects(self::once())
            ->method('callCommandWarmup')
            ->with('employee:warmup 1')
            ->willReturn($commandWarmupMock);

        $this->listener->handle($eventMock);
    }
}
