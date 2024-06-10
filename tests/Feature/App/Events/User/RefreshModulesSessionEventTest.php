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
        $this->event = new RefreshModulesSessionEvent;
    }

    public function tearDown(): void
    {
        unset($this->event);
        parent::tearDown();
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
