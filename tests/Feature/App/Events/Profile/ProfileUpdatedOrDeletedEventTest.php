<?php

namespace Tests\Feature\App\Events\Profile;

use App\Events\Profile\ProfileUpdatedOrDeletedEvent;
use Illuminate\Broadcasting\PrivateChannel;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ProfileUpdatedOrDeletedEvent::class)]
class ProfileUpdatedOrDeletedEventTest extends TestCase
{
    private ProfileUpdatedOrDeletedEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->event = new ProfileUpdatedOrDeletedEvent(1);
    }

    public function tearDown(): void
    {
        unset($this->event);
        parent::tearDown();
    }

    public function testProfileIdShouldReturnInt(): void
    {
        $result = $this->event->profileId();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function testBroadcastOnShouldReturnArrayObject(): void
    {
        $result = $this->event->broadcastOn();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertContainsOnlyInstancesOf(PrivateChannel::class, $result);
    }
}
