<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ModuleIcon;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ModuleIcon::class)]
class ModuleIconTest extends TestCase
{
    private ModuleIcon $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ModuleIcon();
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

    public function testSetValueShouldReturnSelf(): void
    {
        $result = $this->valueObject->setValue('test');

        $this->assertInstanceOf(ModuleIcon::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('test', $result->value());
        $this->assertIsString($result->value());
    }
}
