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
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnNull(): void
    {
        $this->valueObject = new ModuleId();
        $result = $this->valueObject->value();

        $this->assertNull($result);
    }

    public function testSetValueShouldReturnObject(): void
    {
        $this->valueObject = new ModuleId();

        $this->valueObject->setValue(1);
        $this->assertSame(1, $this->valueObject->value());

        $this->valueObject->setValue(10);
        $this->assertSame(10, $this->valueObject->value());
    }

    public function testValueShouldReturnInt(): void
    {
        $this->valueObject = new ModuleId(1);
        $result = $this->valueObject->value();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function testSetValueShouldReturnException(): void
    {
        $this->valueObject = new ModuleId();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Profile\Domain\ValueObjects\ModuleId> does not allow the value <0>.');

        $this->valueObject->setValue(0);
    }

    public function testConstructValueShouldReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Profile\Domain\ValueObjects\ModuleId> does not allow the value <0>.');

        $this->valueObject = new ModuleId(0);
    }

    /**
     * @throws \ReflectionException
     */
    public function testValidateMinRange(): void
    {
        $this->valueObject = new ModuleId();

        $reflection = new \ReflectionClass(ModuleId::class);
        $method = $reflection->getMethod('validate');
        $this->assertTrue($method->isPrivate());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Profile\Domain\ValueObjects\ModuleId> does not allow the value <-1>.');

        $method->invoke($this->valueObject, -1);
    }

    /**
     * @throws \ReflectionException
     */
    public function testValidateAllowsValidValues(): void
    {
        $this->valueObject = new ModuleId();

        $reflection = new \ReflectionClass(ModuleId::class);
        $method = $reflection->getMethod('validate');
        $this->assertTrue($method->isPrivate());

        $method->invoke($this->valueObject, '2');
    }
}
