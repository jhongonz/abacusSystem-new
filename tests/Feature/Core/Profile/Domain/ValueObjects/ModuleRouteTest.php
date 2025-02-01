<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ModuleRoute;
use Tests\TestCase;

class ModuleRouteTest extends TestCase
{
    private ModuleRoute $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ModuleRoute('testing');
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
    }

    public function testSetValueShouldReturnSelf(): void
    {
        $result = $this->valueObject->setValue('test');

        $this->assertInstanceOf(ModuleRoute::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('test', $result->value());
    }
}
