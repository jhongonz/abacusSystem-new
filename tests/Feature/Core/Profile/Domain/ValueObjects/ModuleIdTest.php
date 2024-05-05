<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ModuleId;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ModuleId::class)]
class ModuleIdTest extends TestCase
{
    private ModuleId $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ModuleId();
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
        $this->valueObject = new ModuleId(1);
        $result = $this->valueObject->value();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function test_setValue_should_return_self(): void
    {
        $result = $this->valueObject->setValue(2);

        $this->assertInstanceOf(ModuleId::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(2, $result->value());
    }
}
