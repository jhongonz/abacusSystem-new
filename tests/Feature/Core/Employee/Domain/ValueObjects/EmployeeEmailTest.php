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
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnNull(): void
    {
        $this->valueObject = new EmployeeEmail();

        $result = $this->valueObject->value();
        $this->assertNull($result);
    }

    public function testValueShouldReturnString(): void
    {
        $this->valueObject = new EmployeeEmail('test@test.com');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('test@test.com', $result);
    }

    public function testSetValueShouldChangeAndReturnSelf(): void
    {
        $this->valueObject = new EmployeeEmail();
        $result = $this->valueObject->setValue('test@test.com');

        $this->assertInstanceOf(EmployeeEmail::class, $result);
        $this->assertSame('test@test.com', $this->valueObject->value());
    }

    public function testSetValueShouldException(): void
    {
        $this->valueObject = new EmployeeEmail();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Employee\Domain\ValueObjects\EmployeeEmail> does not allow the invalid email: <test>.');

        $this->valueObject->setValue('test');
    }

    public function testSetValueShouldExceptionWhenConstructWithDataError(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Employee\Domain\ValueObjects\EmployeeEmail> does not allow the invalid email: <test>.');

        $this->valueObject = new EmployeeEmail('test');
    }
}
