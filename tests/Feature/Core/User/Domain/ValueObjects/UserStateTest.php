<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectStatus;
use Core\User\Domain\ValueObjects\UserState;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserState::class)]
class UserStateTest extends TestCase
{
    private UserState $valueObject;

    /**
     * @throws Exception
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
     * @throws Exception
     */
    public function test_value_return_should_integer(): void
    {
        $result = $this->valueObject->value();

        $this->assertSame(ValueObjectStatus::STATE_ACTIVE, $result);
        $this->assertIsInt($result);
    }

    public function test_value_literal_should_return_string(): void
    {
        $result = $this->valueObject->getValueLiteral();

        $this->assertSame('Activo', $result);
        $this->assertIsString($result);
    }

    /**
     * @throws Exception
     */
    public function test_set_value_should_change_state(): void
    {
        $result = $this->valueObject->setValue(2);

        $this->assertInstanceOf(UserState::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(2,$this->valueObject->value());
    }

    public function test_set_value_should_return_exception(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('<Core\User\Domain\ValueObjects\UserState> does not allow the invalid state: <10>');

        $this->valueObject->setValue(10);
    }

    public function test_activate_should_change_state_and_return_int(): void
    {
        $result = $this->valueObject->activate();

        $this->assertInstanceOf(UserState::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(ValueObjectStatus::STATE_ACTIVE, $result->value());
    }

    public function test_inactivate_should_change_state_and_return_int(): void
    {
        $result = $this->valueObject->inactive();

        $this->assertInstanceOf(UserState::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(ValueObjectStatus::STATE_INACTIVE, $result->value());
    }

    public function test_is_new_should_return_bool_and_true(): void
    {
        $result = $this->valueObject->isNew();
        $this->assertIsBool($result);
    }

    /**
     * @throws Exception
     */
    public function test_is_activated_should_return_bool_and_true(): void
    {
        $result = $this->valueObject->isActivated();
        $this->assertIsBool($result);
    }

    public function test_is_inactivated_should_return_bool_and_true(): void
    {
        $result = $this->valueObject->isInactivated();
        $this->assertIsBool($result);
    }

    /**
     * @throws Exception
     */
    public function test_formatHtmlToState_should_return_string_html(): void
    {
        $result = $this->valueObject->formatHtmlToState();
        $this->assertIsString($result);
    }
}
