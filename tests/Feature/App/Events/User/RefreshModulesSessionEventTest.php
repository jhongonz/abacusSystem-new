<?php

namespace Tests\Feature\App\Events\User;

use App\Events\User\RefreshModulesSessionEvent;
use Illuminate\Broadcasting\PrivateChannel;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(RefreshModulesSessionEvent::class)]
class RefreshModulesSessionEventTest extends TestCase
{
    private RefreshModulesSessionEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->event = new RefreshModulesSessionEvent();
    }

    public function tearDown(): void
    {
        unset($this->event);
        parent::tearDown();
    }

    public function testBroadcastOnShouldReturnArrayObject(): void
    {
        $result = $this->event->broadcastOn();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        foreach ($result as $item) {
            $this->assertInstanceOf(PrivateChannel::class, $item);
        }
    }
}
