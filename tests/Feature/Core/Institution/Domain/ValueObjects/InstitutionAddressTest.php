<?php

namespace Tests\Feature\Core\Institution\Domain\ValueObjects;

use Core\Institution\Domain\ValueObjects\InstitutionAddress;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(InstitutionAddress::class)]
class InstitutionAddressTest extends TestCase
{
    private InstitutionAddress $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new InstitutionAddress();
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
        $result = $this->valueObject->setValue('testing');

        $this->assertInstanceOf(InstitutionAddress::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('testing', $result->value());
    }
}
