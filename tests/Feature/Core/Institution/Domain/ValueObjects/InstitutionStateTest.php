<?php

namespace Tests\Feature\Core\Institution\Domain\ValueObjects;

use Core\Institution\Domain\ValueObjects\InstitutionState;
use Core\SharedContext\Model\ValueObjectStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(InstitutionState::class)]
#[CoversClass(ValueObjectStatus::class)]
class InstitutionStateTest extends TestCase
{
    private InstitutionState $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new InstitutionState();
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnInt(): void
    {
        $result = $this->valueObject->value();
        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    /**
     * @throws \Exception
     */
    public function testSetValueShouldReturnSelf(): void
    {
        $result = $this->valueObject->setValue(2);

        $this->assertInstanceOf(InstitutionState::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(2, $result->value());
    }

    public function testSetValueShouldReturnException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('<Core\Institution\Domain\ValueObjects\InstitutionState> does not allow the invalid state: <10>.');

        $this->valueObject->setValue(10);
    }

    public function testValueLiteralShouldReturnWithString(): void
    {
        $result = $this->valueObject->__toString();

        $this->assertSame('Nuevo', $result);
        $this->assertIsString($result);
    }
}
