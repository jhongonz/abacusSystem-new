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
        $this->valueObject = new ModuleCreatedAt;
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

    public function test___toString_should_return_string(): void
    {
        $dateTime = new \DateTime('2024-04-20 21:27:00');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(ModuleCreatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('2024-04-20 21:27:00', (string)$result);
    }
}
