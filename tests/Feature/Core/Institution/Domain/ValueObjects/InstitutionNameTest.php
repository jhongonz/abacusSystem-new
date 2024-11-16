<?php

namespace Tests\Feature\Core\Institution\Domain\ValueObjects;

use Core\Institution\Domain\ValueObjects\InstitutionName;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(InstitutionName::class)]
class InstitutionNameTest extends TestCase
{
    private InstitutionName $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new InstitutionName('Jame');
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnString(): void
    {
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('Jame', $result);
    }

    public function testSetValueShouldReturnSelf(): void
    {
        $result = $this->valueObject->setValue('testing');

        $this->assertInstanceOf(InstitutionName::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('testing', $result->value());
    }
}
