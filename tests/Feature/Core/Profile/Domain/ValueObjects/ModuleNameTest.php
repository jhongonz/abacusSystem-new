<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ModuleName;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ModuleName::class)]
class ModuleNameTest extends TestCase
{
    private ModuleName $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ModuleName('Testing');
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_string(): void
    {
        $result = $this->valueObject->value();
        $this->assertIsString($result);
    }

    public function test_setValue_should_return_self(): void
    {
        $result = $this->valueObject->setValue('test');

        $this->assertInstanceOf(ModuleName::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('test', $result->value());
    }
}
