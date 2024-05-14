<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeImage;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeImage::class)]
class EmployeeImageTest extends TestCase
{
    private EmployeeImage $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeImage;
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
        $this->valueObject->setValue('image');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('image', $result);
    }

    public function test_setValue_should_change_and_return_self(): void
    {
        $result = $this->valueObject->setValue('image');

        $this->assertInstanceOf(EmployeeImage::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('image', $this->valueObject->value());
    }
}
