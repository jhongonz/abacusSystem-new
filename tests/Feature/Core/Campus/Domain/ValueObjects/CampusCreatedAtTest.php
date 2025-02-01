<?php

namespace Tests\Feature\Core\Campus\Domain\ValueObjects;

use Core\Campus\Domain\ValueObjects\CampusCreatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CampusCreatedAt::class)]
class CampusCreatedAtTest extends TestCase
{
    private CampusCreatedAt $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new CampusCreatedAt();
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnDatetime(): void
    {
        $result = $this->valueObject->value();
        $this->assertInstanceOf(\DateTime::class, $result);
    }

    public function testSetValueShouldReturnObject(): void
    {
        $dateTime = new \DateTime('2024-06-21 10:07:00');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(CampusCreatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('2024-06-21 10:07:00', $result->value()->format('Y-m-d h:i:s'));
    }

    public function testToStringShouldReturnString(): void
    {
        $dateTime = new \DateTime('2024-06-21 10:07:00');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(CampusCreatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('2024-06-21 10:07:00', (string) $result);
    }
}
