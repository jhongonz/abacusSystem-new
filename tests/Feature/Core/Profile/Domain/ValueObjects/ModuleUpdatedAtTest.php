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
        $this->valueObject = new ModuleUpdatedAt();
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
        $datetime = new \DateTime('2024-05-05 21:06:00');
        $result = $this->valueObject->setValue($datetime);

        $this->assertInstanceOf(ModuleUpdatedAt::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame($datetime, $result->value());
        $this->assertInstanceOf(\DateTime::class, $result->value());
    }

    public function testToStringShouldReturnString(): void
    {
        $dateTime = new \DateTime('2024-04-20 21:27:00');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(ModuleUpdatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('2024-04-20 21:27:00', (string) $result);
    }
}
