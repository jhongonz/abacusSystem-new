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
        $this->valueObject = new CampusUpdatedAt();
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnNull(): void
    {
        $result = $this->valueObject->value();
        $this->assertNull($result);
    }

    public function testValueShouldReturnDatetime(): void
    {
        $this->valueObject = new CampusUpdatedAt(new \DateTime());
        $result = $this->valueObject->value();
        $this->assertInstanceOf(\DateTime::class, $result);
    }

    public function testSetValueShouldReturnObject(): void
    {
        $dateTime = new \DateTime('2024-06-21 10:07:00');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(CampusUpdatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('2024-06-21 10:07:00', $result->value()->format('Y-m-d h:i:s'));
    }

    public function testToStringShouldReturnString(): void
    {
        $dateTime = new \DateTime('2024-06-21 10:07:00');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(CampusUpdatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('2024-06-21 10:07:00', (string) $result);
    }
}
