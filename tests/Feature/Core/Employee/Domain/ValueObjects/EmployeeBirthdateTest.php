<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeBirthdate;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeBirthdate::class)]
class EmployeeBirthdateTest extends TestCase
{
    private EmployeeBirthdate $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeBirthdate();
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
        $datetime = new \DateTime('2024-04-26');
        $this->valueObject = new EmployeeBirthdate($datetime);
        $result = $this->valueObject->value();

        $this->assertNotNull($result);
        $this->assertInstanceOf(\DateTime::class, $datetime);
        $this->assertSame($datetime, $result);
    }

    public function testValueShouldReturnDateToString(): void
    {
        $datetime = new \DateTime('2024-04-26');
        $this->valueObject = new EmployeeBirthdate($datetime);
        $result = $this->valueObject->toFormattedString();

        $this->assertNotNull($result);
        $this->assertIsString($result);
        $this->assertSame('26/04/2024', $result);
    }

    public function testSetValueShouldChangeAndReturnSelf(): void
    {
        $datetime = new \DateTime('2024-04-26');
        $result = $this->valueObject->setValue($datetime);

        $this->assertSame($result, $this->valueObject);
        $this->assertSame($datetime, $result->value());
        $this->assertInstanceOf(\DateTime::class, $this->valueObject->value());
    }

    public function testToStringShouldReturnString(): void
    {
        $dateTime = new \DateTime('2024-04-26');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(EmployeeBirthdate::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('26/04/2024', (string) $result);
    }
}
