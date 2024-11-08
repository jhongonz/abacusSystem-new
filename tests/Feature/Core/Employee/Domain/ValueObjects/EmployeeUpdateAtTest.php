<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeUpdatedAt;
use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeUpdatedAt::class)]
class EmployeeUpdateAtTest extends TestCase
{
    private EmployeeUpdatedAt $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeUpdatedAt;
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

        $this->assertInstanceOf(EmployeeUpdatedAt::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame($datetime, $this->valueObject->value());
    }

    public function test___toString_should_return_string(): void
    {
        $dateTime = new \DateTime('2024-04-20 21:27:00');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(EmployeeUpdatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('2024-04-20 21:27:00', (string)$result);
    }
}
