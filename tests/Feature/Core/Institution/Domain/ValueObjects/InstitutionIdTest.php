<?php

namespace Tests\Feature\Core\Institution\Domain\ValueObjects;

use Core\Institution\Domain\ValueObjects\InstitutionId;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(InstitutionId::class)]
class InstitutionIdTest extends TestCase
{
    private InstitutionId $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new InstitutionId();
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

    public function testConstructWithId(): void
    {
        $this->valueObject = new InstitutionId(1);
        $this->assertSame(1, $this->valueObject->value());
    }

    public function testSetValueShouldReturnSelf(): void
    {
        $result = $this->valueObject->setValue(1);

        $this->assertInstanceOf(InstitutionId::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(1, $result->value());
    }

    public function testSetValueShouldReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Institution\Domain\ValueObjects\InstitutionId> does not allow the value <0>.');

        $this->valueObject->setValue(0);
    }

    public function testConstructShouldReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Institution\Domain\ValueObjects\InstitutionId> does not allow the value <0>.');

        $this->valueObject = new InstitutionId(0);
    }
}
