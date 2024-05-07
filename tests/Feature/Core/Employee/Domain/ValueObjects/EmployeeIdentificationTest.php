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

    public function test_value_should_return_null(): void
    {
        $result = $this->valueObject->value();
        $this->assertNull($result);
    }

    public function test_value_should_return_string(): void
    {
        $this->valueObject->setValue('12345');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('12345', $result);
    }

    public function test_setValue_should_change_and_return_self(): void
    {
        $result = $this->valueObject->setValue('12345');

        $this->assertInstanceOf(EmployeeIdentification::class, $result);
        $this->assertSame('12345', $this->valueObject->value());
    }
}
