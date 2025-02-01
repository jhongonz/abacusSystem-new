<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeAddress;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeAddress::class)]
class EmployeeAddressTest extends TestCase
{
    private EmployeeAddress $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeAddress();
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnNull(): void
    {
        $return = $this->valueObject->value();
        $this->assertNull($return);
    }

    public function testValueShouldReturnString(): void
    {
        $this->valueObject = new EmployeeAddress('address');
        $return = $this->valueObject->value();

        $this->assertIsString($return);
        $this->assertSame('address', $return);
    }

    public function testSetValueShouldChangeValueAndReturnSelf(): void
    {
        $result = $this->valueObject->setValue('address');

        $this->assertSame($result, $this->valueObject);
        $this->assertInstanceOf(EmployeeAddress::class, $result);
        $this->assertNotNull($result->value());
        $this->assertSame('address', $this->valueObject->value());
    }
}
