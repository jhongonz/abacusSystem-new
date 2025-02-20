<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2025-02-19 07:43:38
 */

namespace Feature\App\Events\Institution;

use App\Events\Institution\InstitutionUpdateOrDeletedEvent;
use Illuminate\Broadcasting\PrivateChannel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InstitutionUpdateOrDeletedEvent::class)]
class InstitutionUpdateOrDeletedEventTest extends TestCase
{
    private InstitutionUpdateOrDeletedEvent $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->event = new InstitutionUpdateOrDeletedEvent(1);
    }

    protected function tearDown(): void
    {
        unset($this->event);
        parent::tearDown();
    }

    public function testInstitutionIdShouldReturnInt(): void
    {
        $result = $this->event->institutionId();

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
