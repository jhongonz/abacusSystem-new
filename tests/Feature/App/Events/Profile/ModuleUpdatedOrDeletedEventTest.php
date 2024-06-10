<?php

namespace Tests\Feature\App\Events\Profile;

use App\Events\Profile\ModuleUpdatedOrDeletedEvent;
use Illuminate\Broadcasting\PrivateChannel;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ModuleUpdatedOrDeletedEvent::class)]
class ModuleUpdatedOrDeletedEventTest extends TestCase
{
    private ModuleUpdatedOrDeletedEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->event = new ModuleUpdatedOrDeletedEvent(1);
    }

    public function tearDown(): void
    {
        unset($this->event);
        parent::tearDown();
    }

    public function test_moduleId_should_return_int(): void
    {
        $result = $this->event->moduleId();

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
