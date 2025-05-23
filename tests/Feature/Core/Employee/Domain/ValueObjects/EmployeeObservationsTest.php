<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeObservations;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeObservations::class)]
class EmployeeObservationsTest extends TestCase
{
    private EmployeeObservations $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeObservations();
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
        $this->valueObject->setValue('testing');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('testing', $result);
    }

    public function testSetValueShouldChangeAndReturnSelf(): void
    {
        $result = $this->valueObject->setValue('testing');

        $this->assertInstanceOf(EmployeeObservations::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('testing', $this->valueObject->value());
    }
}
