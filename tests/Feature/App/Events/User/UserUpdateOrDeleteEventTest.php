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

    public function test_userId_should_return_int(): void
    {
        $result = $this->event->userId();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function test_broadcastOn_should_return_array_object(): void
    {
        $result = $this->event->broadcastOn();

        $this->assertIsArray($result);
        foreach ($result as $item) {
            $this->assertInstanceOf(PrivateChannel::class, $item);
        }
    }
}
