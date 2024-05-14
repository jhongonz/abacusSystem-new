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
        $this->valueObject = new EmployeeAddress;
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_null(): void
    {
        $return = $this->valueObject->value();
        $this->assertNull($return);
    }

    public function test_value_should_return_string(): void
    {
        $this->valueObject = new EmployeeAddress('address');
        $return = $this->valueObject->value();

        $this->assertIsString($return);
        $this->assertSame('address', $return);
    }

    public function test_setValue_should_change_value_and_return_self(): void
    {
        $result = $this->valueObject->setValue('address');

        $this->assertSame($result, $this->valueObject);
        $this->assertInstanceOf(EmployeeAddress::class, $result);
        $this->assertNotNull($result->value());
        $this->assertSame('address', $this->valueObject->value());
    }
}
