<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ModuleUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ModuleUpdatedAt::class)]
class ModuleUpdatedAtTest extends TestCase
{
    private ModuleUpdatedAt $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ModuleUpdatedAt;
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

    public function test_setValue_should_return_self(): void
    {
        $datetime = new \DateTime('2024-05-05 21:06:00');
        $result = $this->valueObject->setValue($datetime);

        $this->assertInstanceOf(ModuleUpdatedAt::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame($datetime, $result->value());
        $this->assertInstanceOf(\DateTime::class, $result->value());
    }
}
