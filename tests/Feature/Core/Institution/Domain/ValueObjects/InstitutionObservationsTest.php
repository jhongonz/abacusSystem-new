<?php

namespace Tests\Feature\Core\Institution\Domain\ValueObjects;

use Core\Institution\Domain\ValueObjects\InstitutionObservations;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(InstitutionObservations::class)]
class InstitutionObservationsTest extends TestCase
{
    private InstitutionObservations $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new InstitutionObservations;
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
        $result = $this->valueObject->setValue('testing');

        $this->assertInstanceOf(InstitutionObservations::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('testing', $result->value());
    }
}
