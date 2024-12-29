<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeId;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeId::class)]
class EmployeeIdTest extends TestCase
{
    private EmployeeId $valueObject;

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
        $this->valueObject = new EmployeeId();

        $result = $this->valueObject->value();
        $this->assertNull($result);
    }

    public function testValueShouldReturnInt(): void
    {
        $this->valueObject = new EmployeeId(2);
        $result = $this->valueObject->value();

        $this->assertIsInt($result);
        $this->assertSame($result, 2);
    }

    public function testSetValueShouldChangeAndReturnSelf(): void
    {
        $this->valueObject = new EmployeeId(1);
        $result = $this->valueObject->setValue(2);

        $this->assertInstanceOf(EmployeeId::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame(2, $this->valueObject->value());
    }

    public function testSetValueShouldReturnException(): void
    {
        $this->valueObject = new EmployeeId(1);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Employee\Domain\ValueObjects\EmployeeId> does not allow the value <0>.');

        $this->valueObject->setValue(0);
    }

    public function testSetValueShouldReturnExceptionWhenIsConstructWithIdIncorrectly(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Employee\Domain\ValueObjects\EmployeeId> does not allow the value <0>.');

        $this->valueObject = new EmployeeId(0);
    }
}
