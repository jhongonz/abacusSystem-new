<?php

namespace Tests\Feature\Core\Campus\Domain\ValueObjects;

use Core\Campus\Domain\ValueObjects\CampusObservations;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CampusObservations::class)]
class CampusObservationsTest extends TestCase
{
    private CampusObservations $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new CampusObservations();
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

    public function testValueShouldReturnString(): void
    {
        $this->valueObject->setValue('testing');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('testing', $result);
    }

    public function testSetValueShouldReturnObject(): void
    {
        $result = $this->valueObject->setValue('sandbox');

        $this->assertInstanceOf(CampusObservations::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('sandbox', $result->value());
    }
}
