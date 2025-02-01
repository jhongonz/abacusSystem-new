<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ModuleMenuKey;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ModuleMenuKey::class)]
class ModuleMenuKeyTest extends TestCase
{
    private ModuleMenuKey $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ModuleMenuKey('test');
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnString(): void
    {
        $result = $this->valueObject->value();
        $this->assertIsString($result);
        $this->assertSame('test', $result);
    }

    public function testSetValueShouldReturnSelf(): void
    {
        $result = $this->valueObject->setValue('test');

        $this->assertInstanceOf(ModuleMenuKey::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('test', $result->value());
        $this->assertIsString($result->value());
    }
}
