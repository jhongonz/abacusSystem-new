<?php

namespace Tests\Feature\App\Events\Employee;

use App\Events\Employee\EmployeeUpdateOrDeletedEvent;
use Illuminate\Broadcasting\PrivateChannel;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeUpdateOrDeletedEvent::class)]
class EmployeeUpdateOrDeletedEventTest extends TestCase
{
    private EmployeeUpdateOrDeletedEvent $event;

    public function setUp(): void
    {
        parent::setUp();
        $this->event = new EmployeeUpdateOrDeletedEvent(1);
    }

    public function tearDown(): void
    {
        unset(
            $this->event
        );
        parent::tearDown();
    }

    public function testEmployeeIdShouldReturnInt(): void
    {
        $result = $this->event->employeeId();

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
