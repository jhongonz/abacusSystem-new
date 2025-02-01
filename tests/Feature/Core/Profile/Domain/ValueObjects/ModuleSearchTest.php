<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ModuleSearch;
use Tests\TestCase;

class ModuleSearchTest extends TestCase
{
    private ModuleSearch $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ModuleSearch();
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
        $result = $this->valueObject->setValue('hello world');

        $this->assertInstanceOf(ModuleSearch::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('hello world', $result->value());
        $this->assertIsString($result->value());
    }
}
