<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeSearch;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeSearch::class)]
class EmployeeSearchTest extends TestCase
{
    private EmployeeSearch $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeSearch();
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
        $this->valueObject->setValue('context');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('context', $result);
    }

    public function test_setValue_should_change_and_return_self(): void
    {
        $result = $this->valueObject->setValue('context');

        $this->assertInstanceOf(EmployeeSearch::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('context', $this->valueObject->value());
    }
}
