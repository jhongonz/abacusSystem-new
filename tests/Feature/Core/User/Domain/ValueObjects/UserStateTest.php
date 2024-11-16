<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectStatus;
use Core\User\Domain\ValueObjects\UserState;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserState::class)]
#[CoversClass(ValueObjectStatus::class)]
class UserStateTest extends TestCase
{
    private UserState $valueObject;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new UserState();
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    /**
     * @throws \Exception
     */
    public function testValueReturnShouldInteger(): void
    {
        $result = $this->valueObject->value();

        $this->assertSame(ValueObjectStatus::STATE_ACTIVE, $result);
        $this->assertIsInt($result);
    }

    public function testValueLiteralShouldReturnString(): void
    {
        $result = $this->valueObject->getValueLiteral();

        $this->assertSame('Activo', $result);
        $this->assertIsString($result);
    }

    public function testValueLiteralShouldReturnWithString(): void
    {
        $result = $this->valueObject->__toString();

        $this->assertSame('Activo', $result);
        $this->assertIsString($result);
    }

    /**
     * @throws \Exception
     */
    public function testSetValueShouldChangeState(): void
    {
        $result = $this->valueObject->setValue(2);

        $this->assertInstanceOf(UserState::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(2, $this->valueObject->value());
    }

    public function testSetValueShouldReturnException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('<Core\User\Domain\ValueObjects\UserState> does not allow the invalid state: <10>');

        $this->valueObject->setValue(10);
    }

    public function testActivateShouldChangeStateAndReturnInt(): void
    {
        $result = $this->valueObject->activate();

        $this->assertInstanceOf(UserState::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(ValueObjectStatus::STATE_ACTIVE, $result->value());
    }

    public function testInactivateShouldChangeStateAndReturnInt(): void
    {
        $result = $this->valueObject->inactive();

        $this->assertInstanceOf(UserState::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(ValueObjectStatus::STATE_INACTIVE, $result->value());
    }

    public function testIsNewShouldReturnBoolAndTrue(): void
    {
        $result = $this->valueObject->isNew();
        $this->assertIsBool($result);
    }

    /**
     * @throws \Exception
     */
    public function testIsActivatedShouldReturnBoolAndTrue(): void
    {
        $result = $this->valueObject->isActivated();
        $this->assertIsBool($result);
    }

    public function testIsInactivatedShouldReturnBoolAndTrue(): void
    {
        $result = $this->valueObject->isInactivated();
        $this->assertIsBool($result);
    }

    /**
     * @throws \Exception
     */
    public function testFormatHtmlToStateShouldReturnStringHtml(): void
    {
        $result = $this->valueObject->formatHtmlToState();
        $this->assertIsString($result);
    }
}
