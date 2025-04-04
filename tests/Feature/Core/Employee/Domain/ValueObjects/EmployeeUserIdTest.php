<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeUserId;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeUserId::class)]
class EmployeeUserIdTest extends TestCase
{
    private EmployeeUserId $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeUserId();
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

    public function testValueShouldReturnInt(): void
    {
        $this->valueObject->setValue(1);
        $result = $this->valueObject->value();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function testValueShouldReturnNullWhenIsChange(): void
    {
        $this->valueObject = new EmployeeUserId(1);
        $this->valueObject->setValue(null);

        $result = $this->valueObject->value();

        $this->assertNull($result);
    }

    public function testSetValueShouldChangeAndReturnSelf(): void
    {
        $result = $this->valueObject->setValue(1);

        $this->assertInstanceOf(EmployeeUserId::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(1, $this->valueObject->value());
    }
}
