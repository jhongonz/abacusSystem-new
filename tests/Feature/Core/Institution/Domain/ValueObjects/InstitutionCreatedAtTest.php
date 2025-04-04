<?php

namespace Tests\Feature\Core\Institution\Domain\ValueObjects;

use Core\Institution\Domain\ValueObjects\InstitutionCreatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(InstitutionCreatedAt::class)]
class InstitutionCreatedAtTest extends TestCase
{
    private InstitutionCreatedAt $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new InstitutionCreatedAt();
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnObject(): void
    {
        $result = $this->valueObject->value();
        $this->assertInstanceOf(\DateTime::class, $result);
    }

    public function testSetValueShouldReturnSelf(): void
    {
        $datetime = new \DateTime('2024-05-19 18:05:00');
        $result = $this->valueObject->setValue($datetime);

        $this->assertInstanceOf(InstitutionCreatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame($datetime, $this->valueObject->value());
    }

    public function testToStringShouldReturnString(): void
    {
        $dateTime = new \DateTime('2024-04-20 21:27:00');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(InstitutionCreatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('2024-04-20 21:27:00', (string) $result);
    }
}
