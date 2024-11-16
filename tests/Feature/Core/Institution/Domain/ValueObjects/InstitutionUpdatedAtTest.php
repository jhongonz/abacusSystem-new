<?php

namespace Tests\Feature\Core\Institution\Domain\ValueObjects;

use Core\Institution\Domain\ValueObjects\InstitutionUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(InstitutionUpdatedAt::class)]
class InstitutionUpdatedAtTest extends TestCase
{
    private InstitutionUpdatedAt $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new InstitutionUpdatedAt();
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
        $datetime = new \DateTime('2024-05-19 18:05:00');
        $result = $this->valueObject->setValue($datetime);

        $this->assertInstanceOf(InstitutionUpdatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame($datetime, $this->valueObject->value());
    }

    public function testToStringShouldReturnString(): void
    {
        $dateTime = new \DateTime('2024-04-20 21:27:00');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(InstitutionUpdatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('2024-04-20 21:27:00', (string) $result);
    }
}
