<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeCreatedAt;
use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeCreatedAt::class)]
class EmployeeCreatedAtTest extends TestCase
{
    private EmployeeCreatedAt $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeCreatedAt();
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_datetime(): void
    {
        $result = $this->valueObject->value();
        $this->assertInstanceOf(DateTime::class, $result);
    }

    public function test_setValue_should_change_and_return_self(): void
    {
        $datetime = new DateTime('2024-04-26');
        $result = $this->valueObject->setValue($datetime);

        $this->assertInstanceOf(EmployeeCreatedAt::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame($datetime, $result->value());
    }
}
