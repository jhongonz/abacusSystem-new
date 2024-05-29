<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ModulePosition;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ModulePosition::class)]
class ModulePositionTest extends TestCase
{
    private ModulePosition $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ModulePosition;
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_null(): void
    {
        $result = $this->valueObject->value();
        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function test_value_should_return_int(): void
    {
        $this->valueObject = new ModulePosition(2);
        $result = $this->valueObject->value();

        $this->assertIsInt($result);
        $this->assertSame(2, $result);
    }

    public function test_setValue_should_return_self(): void
    {
        $result = $this->valueObject->setValue(2);

        $this->assertInstanceOf(ModulePosition::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(2, $result->value());
    }
}
