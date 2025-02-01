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

    public function testValueShouldReturnNull(): void
    {
        $result = $this->valueObject->value();
        $this->assertNull($result);
    }

    public function testValueShouldReturnString(): void
    {
        $this->valueObject->setValue('context');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('context', $result);
    }

    public function testSetValueShouldChangeAndReturnSelf(): void
    {
        $result = $this->valueObject->setValue('context');

        $this->assertInstanceOf(EmployeeSearch::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('context', $this->valueObject->value());
    }
}
