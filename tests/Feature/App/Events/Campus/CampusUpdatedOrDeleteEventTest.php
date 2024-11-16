<?php

namespace Tests\Feature\App\Events\Campus;

use App\Events\Campus\CampusUpdatedOrDeletedEvent;
use Illuminate\Broadcasting\PrivateChannel;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CampusUpdatedOrDeletedEvent::class)]
class CampusUpdatedOrDeleteEventTest extends TestCase
{
    private CampusUpdatedOrDeletedEvent $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->event = new CampusUpdatedOrDeletedEvent(1);
    }

    protected function tearDown(): void
    {
        unset($this->event);
        parent::tearDown();
    }

    public function testCampusIdShouldReturnInt(): void
    {
        $result = $this->event->campusId();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function testBroadcastOnShouldReturnArrayObject(): void
    {
        $result = $this->event->broadcastOn();

        $this->assertIsArray($result);
        $this->assertContainsOnlyInstancesOf(PrivateChannel::class, $result);
    }
}
