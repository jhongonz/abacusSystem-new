<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeIdentification::class)]
class EmployeeIdentificationTest extends TestCase
{
    private EmployeeIdentification $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeIdentification();
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

    public function testValueShouldReturnString(): void
    {
        $this->valueObject->setValue('12345');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('12345', $result);
    }

    public function testSetValueShouldChangeAndReturnSelf(): void
    {
        $result = $this->valueObject->setValue('12345');

        $this->assertInstanceOf(EmployeeIdentification::class, $result);
        $this->assertSame('12345', $this->valueObject->value());
    }
}
