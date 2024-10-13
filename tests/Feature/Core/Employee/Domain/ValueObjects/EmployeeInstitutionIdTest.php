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
        $this->valueObject = new EmployeeInstitutionId;
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

    public function test_value_should_return_int(): void
    {
        $this->valueObject = new EmployeeInstitutionId(2);
        $result = $this->valueObject->value();

        $this->assertIsInt($result);
        $this->assertSame($result, 2);
    }

    public function test_setValue_should_change_and_return_self(): void
    {
        $result = $this->valueObject->setValue(2);

        $this->assertInstanceOf(EmployeeInstitutionId::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame(2, $this->valueObject->value());
    }

    public function test_setValue_should_return_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Employee\Domain\ValueObjects\EmployeeInstitutionId> does not allow the value <0>.');

        $this->valueObject->setValue(0);
    }
}
