<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ModuleCreatedAt;
use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ModuleCreatedAt::class)]
class ModuleCreatedAtTest extends TestCase
{
    private ModuleCreatedAt $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ModuleCreatedAt();
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_datetime(): void
    {
        $result = $this->valueObject->value();
        $this->assertInstanceOf(DateTime::class, $result);
    }

    public function test_setValue_should_return_self(): void
    {
        $datetime = new DateTime('2024-05-05');
        $result = $this->valueObject->setValue($datetime);

        $this->assertInstanceOf(ModuleCreatedAt::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame($datetime, $result->value());
    }
}
