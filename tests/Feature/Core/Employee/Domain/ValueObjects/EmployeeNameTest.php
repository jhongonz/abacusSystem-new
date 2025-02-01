<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeName;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeName::class)]
class EmployeeNameTest extends TestCase
{
    private EmployeeName $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeName('Jhon');
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnString(): void
    {
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('Jhon', $result);
    }

    public function testSetValueShouldChangeAndReturnSelf(): void
    {
        $result = $this->valueObject->setValue('Peter');

        $this->assertInstanceOf(EmployeeName::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('Peter', $this->valueObject->value());
    }
}
