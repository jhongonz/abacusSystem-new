<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeLastname;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeLastname::class)]
class EmployeeLastnameTest extends TestCase
{
    private EmployeeLastname $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeLastname();
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_null(): void
    {
        $result = $this->valueObject->value();
        $this->assertNull($result);
    }

    public function test_value_should_return_string(): void
    {
        $this->valueObject->setValue('lastname');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('lastname', $result);
    }

    public function test_setValue_should_change_and_return_self(): void
    {
        $result = $this->valueObject->setValue('lastname');

        $this->assertInstanceOf(EmployeeLastname::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('lastname', $this->valueObject->value());
    }
}
