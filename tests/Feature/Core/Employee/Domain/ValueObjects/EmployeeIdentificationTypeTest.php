<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeIdentificationType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeIdentificationType::class)]
class EmployeeIdentificationTypeTest extends TestCase
{
    private EmployeeIdentificationType $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeIdentificationType();
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
        $this->valueObject->setValue('type');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('type', $result);
    }

    public function testSetValueShouldChangeAndReturnSelf(): void
    {
        $result = $this->valueObject->setValue('type');

        $this->assertInstanceOf(EmployeeIdentificationType::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('type', $this->valueObject->value());
    }
}
