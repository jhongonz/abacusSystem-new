<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeUpdatedAt::class)]
class EmployeeUpdateAtTest extends TestCase
{
    private EmployeeUpdatedAt $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeUpdatedAt();
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

    public function testValueShouldReturnDateTime(): void
    {
        $datetime = new \DateTime();
        $this->valueObject->setValue($datetime);
        $result = $this->valueObject->value();

        $this->assertInstanceOf(\DateTime::class, $result);
        $this->assertSame($result, $datetime);
    }

    public function testSetValueShouldChangeAndReturnSelf(): void
    {
        $datetime = new \DateTime();
        $result = $this->valueObject->setValue($datetime);

        $this->assertInstanceOf(EmployeeUpdatedAt::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame($datetime, $this->valueObject->value());
    }

    public function testToStringShouldReturnString(): void
    {
        $dateTime = new \DateTime('2024-04-20 21:27:00');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(EmployeeUpdatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('2024-04-20 21:27:00', (string) $result);
    }
}
