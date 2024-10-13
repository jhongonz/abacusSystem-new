<?php

namespace Tests\Feature\Core\Campus\Domain\ValueObjects;

use Core\Campus\Domain\ValueObjects\CampusUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CampusUpdatedAt::class)]
class CampusUpdatedAtTest extends TestCase
{
    private CampusUpdatedAt $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new CampusUpdatedAt;
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_null(): void
    {
        $result = $this->valueObject->value();
        $this->assertNull($result);
    }

    public function test_value_should_return_datetime(): void
    {
        $this->valueObject = new CampusUpdatedAt(new \DateTime);
        $result = $this->valueObject->value();
        $this->assertInstanceOf(\DateTime::class, $result);
    }

    public function test_setValue_should_return_object(): void
    {
        $dateTime = new \DateTime('2024-06-21 10:07:00');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(CampusUpdatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('2024-06-21 10:07:00', $result->value()->format('Y-m-d h:i:s'));
    }

    public function test___toString_should_return_string(): void
    {
        $dateTime = new \DateTime('2024-06-21 10:07:00');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(CampusUpdatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('2024-06-21 10:07:00', (string)$result);
    }
}
