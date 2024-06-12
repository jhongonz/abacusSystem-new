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

    public function test_employeeId_should_return_int(): void
    {
        $result = $this->event->employeeId();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function test_broadcastOn_should_return_array_object(): void
    {
        $result = $this->event->broadcastOn();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        foreach ($result as $item) {
            $this->assertInstanceOf(PrivateChannel::class, $item);
        }
    }
}
