<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeInstitutionId;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeInstitutionId::class)]
class EmployeeInstitutionIdTest extends TestCase
{
    private EmployeeInstitutionId $valueObject;

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
        $this->valueObject = new EmployeeInstitutionId();

        $result = $this->valueObject->value();
        $this->assertNull($result);
    }

    public function testValueShouldReturnInt(): void
    {
        $this->valueObject = new EmployeeInstitutionId(2);
        $result = $this->valueObject->value();

        $this->assertIsInt($result);
        $this->assertSame($result, 2);
    }

    public function testSetValueShouldChangeAndReturnSelf(): void
    {
        $this->valueObject = new EmployeeInstitutionId(1);
        $result = $this->valueObject->setValue(2);

        $this->assertInstanceOf(EmployeeInstitutionId::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame(2, $this->valueObject->value());
    }

    public function testSetValueShouldReturnException(): void
    {
        $this->valueObject = new EmployeeInstitutionId(1);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Employee\Domain\ValueObjects\EmployeeInstitutionId> does not allow the value <0>.');

        $this->valueObject->setValue(0);
    }

    public function testSetValueShouldReturnExceptionWhenIsConstructWithIdIncorrectly(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Employee\Domain\ValueObjects\EmployeeInstitutionId> does not allow the value <0>.');

        $this->valueObject = new EmployeeInstitutionId(0);
    }
}
