<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeUpdateAt;
use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeUpdateAt::class)]
class EmployeeUpdateAtTest extends TestCase
{
    private EmployeeUpdateAt $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeUpdateAt;
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

    public function test_value_should_return_DateTime(): void
    {
        $datetime = new DateTime;
        $this->valueObject->setValue($datetime);
        $result = $this->valueObject->value();

        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertSame($result, $datetime);
    }

    public function test_setValue_should_change_and_return_self(): void
    {
        $datetime = new DateTime;
        $result = $this->valueObject->setValue($datetime);

        $this->assertInstanceOf(EmployeeUpdateAt::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame($datetime, $this->valueObject->value());
    }
}
