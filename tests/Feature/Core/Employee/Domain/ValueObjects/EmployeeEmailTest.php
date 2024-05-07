<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeEmail;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeEmail::class)]
class EmployeeEmailTest extends TestCase
{
    private EmployeeEmail $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeEmail();
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
        $this->valueObject = new EmployeeEmail('test@test.com');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('test@test.com', $result);
    }

    public function test_setValue_should_change_and_return_self(): void
    {
        $result = $this->valueObject->setValue('test@test.com');

        $this->assertInstanceOf(EmployeeEmail::class, $result);
        $this->assertSame('test@test.com', $this->valueObject->value());
    }

    public function test_setValue_should_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Employee\Domain\ValueObjects\EmployeeEmail> does not allow the invalid email: <test>.');

        $this->valueObject->setValue('test');
    }
}
