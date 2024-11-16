<?php

namespace Tests\Feature\Core\Institution\Domain\ValueObjects;

use Core\Institution\Domain\ValueObjects\InstitutionLogo;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(InstitutionLogo::class)]
class InstitutionLogoTest extends TestCase
{
    private InstitutionLogo $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new InstitutionLogo();
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

        $this->assertInstanceOf(InstitutionLogo::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('testing', $result->value());
    }
}
