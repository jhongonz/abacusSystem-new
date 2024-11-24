<?php

namespace Tests\Feature\App\Events\User;

use App\Events\User\UserUpdateOrDeleteEvent;
use Illuminate\Broadcasting\PrivateChannel;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserUpdateOrDeleteEvent::class)]
class UserUpdateOrDeleteEventTest extends TestCase
{
    private UserUpdateOrDeleteEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->event = new UserUpdateOrDeleteEvent(1);
    }

    public function tearDown(): void
    {
        unset($this->event);
        parent::tearDown();
    }

    public function testUserIdShouldReturnInt(): void
    {
        $result = $this->event->userId();

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
