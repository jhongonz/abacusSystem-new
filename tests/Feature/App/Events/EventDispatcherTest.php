<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-15 10:17:46
 */

namespace Tests\Feature\App\Events;

use App\Events\EventDispatcher;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(EventDispatcher::class)]
class EventDispatcherTest extends TestCase
{
    private EventDispatcher $eventDispatcher;
    private Dispatcher|MockObject $dispatcherMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->dispatcherMock = $this->createMock(Dispatcher::class);
        $this->eventDispatcher = new EventDispatcher($this->dispatcherMock);
    }

    protected function tearDown(): void
    {
        unset($this->eventDispatcher, $this->dispatcherMock);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testEventDispatch(): void
    {
        $eventMock = new class {};
        $this->dispatcherMock->expects(self::once())
            ->method('dispatch')
            ->with($eventMock)
            ->willReturn(['testing event']);

        $this->eventDispatcher->dispatch($eventMock);
    }
}
