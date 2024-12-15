<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-15 10:17:46
 */

namespace Tests\Feature\App\Events;

use App\Events\Campus\CampusUpdatedOrDeletedEvent;
use App\Events\EventDispatcher;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(EventDispatcher::class)]
class EventDispatcherTest extends TestCase
{
    private EventDispatcher $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventDispatcher = new EventDispatcher();
    }

    protected function tearDown(): void
    {
        unset($this->eventDispatcher);
        parent::tearDown();
    }

    public function testEventDispatch(): void
    {
        Event::fake();

        $campusId = 1;
        $this->executeEvent($campusId);

        Event::assertDispatched(CampusUpdatedOrDeletedEvent::class, function ($event) use ($campusId) {
            /** @var CampusUpdatedOrDeletedEvent $event */
            return $event->campusId() === $campusId;
        });
    }

    private function executeEvent(int $campusId): void
    {
        /**
         * This function can be evaluated with another event of the project.
         */
        $this->eventDispatcher->dispatch(new CampusUpdatedOrDeletedEvent($campusId));
    }
}
