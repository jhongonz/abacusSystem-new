<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeBirthdate;
use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeBirthdate::class)]
class EmployeeBirthdateTest extends TestCase
{
    private EmployeeBirthdate $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeBirthdate;
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

    public function test_value_should_return_datetime(): void
    {
        $datetime = new DateTime('2024-04-26');
        $this->valueObject = new EmployeeBirthdate($datetime);
        $result = $this->valueObject->value();

        $this->assertNotNull($result);
        $this->assertInstanceOf(DateTime::class, $datetime);
        $this->assertSame($datetime, $result);
    }

    public function test_value_should_return_date_toString(): void
    {
        $datetime = new DateTime('2024-04-26');
        $this->valueObject = new EmployeeBirthdate($datetime);
        $result = $this->valueObject->toFormattedString();

        $this->assertNotNull($result);
        $this->assertIsString($result);
        $this->assertSame('26/04/2024', $result);
    }

    public function test_setValue_should_change_and_return_self(): void
    {
        $datetime = new DateTime('2024-04-26');
        $result = $this->valueObject->setValue($datetime);

        $this->assertSame($result, $this->valueObject);
        $this->assertSame($datetime, $result->value());
        $this->assertInstanceOf(DateTime::class, $this->valueObject->value());
    }

    public function test___toString_should_return_string(): void
    {
        $dateTime = new \DateTime('2024-04-26');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(EmployeeBirthdate::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('26/04/2024', (string)$result);
    }
}
