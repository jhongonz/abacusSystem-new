<?php

namespace Tests\Feature\Core\User\Domain\ValueObjects;

use Core\User\Domain\ValueObjects\UserState;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JetBrains\PhpStorm\NoReturn;
use Tests\TestCase;

class UserStateTest extends TestCase
{
    private UserState $valueObject;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new UserState(1);
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
        $expected = 1;
        $result = $this->valueObject->value();

        $this->assertSame($expected, $result);
        $this->assertIsInt($result);
    }

    public function test_value_literal_should_return_string(): void
    {
        $expected = 'Nuevo';
        $result = $this->valueObject->getValueLiteral();

        $this->assertSame($expected, $result);
        $this->assertIsString($result);
    }

    /**
     * @throws Exception
     */
    public function test_set_value_should_change_state(): void
    {
        $original = $this->valueObject->value();
        $expected = 2;
        $object = $this->valueObject->setValue($expected);

        $result = $this->valueObject->value();
        $this->assertInstanceOf(UserState::class, $object);
        $this->assertNotEquals($expected, $original);
        $this->assertSame($expected, $result);
        $this->assertIsInt($result);
    }

    public function test_set_value_should_return_exception(): void
    {
        $expected = 10;
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('<Core\User\Domain\ValueObjects\UserState> does not allow the invalid state: <10>');

        $this->valueObject->setValue($expected);
    }

    public function test_activate_should_change_state_and_return_int(): void
    {
        $original = $this->valueObject->value();
        $expected = 2;

        $this->valueObject->activate();
        $result = $this->valueObject->value();

        $this->assertSame($expected, $result);
        $this->assertIsInt($result);
        $this->assertNotEquals($original, $result);
    }

    public function test_inactivate_should_change_state_and_return_int(): void
    {
        $original = $this->valueObject->value();
        $expected = 3;

        $this->valueObject->inactive();
        $result = $this->valueObject->value();

        $this->assertSame($expected, $result);
        $this->assertIsInt($result);
        $this->assertNotEquals($original, $result);
    }

    public function test_is_new_should_return_bool_and_true(): void
    {
        $result = $this->valueObject->isNew();

        $this->assertSame(true, $result);
        $this->assertIsBool($result);
    }

    public function test_is_actived_should_return_bool_and_true(): void
    {
        $this->valueObject->setValue(2);
        $result = $this->valueObject->isActived();

        $this->assertSame(true, $result);
        $this->assertIsBool($result);
    }

    public function test_is_inactived_should_return_bool_and_true(): void
    {
        $this->valueObject->setValue(3);
        $result = $this->valueObject->isInactived();

        $this->assertSame(true, $result);
        $this->assertIsBool($result);
    }

    /**
     * @throws Exception
     */
    public function test_formatHtmlToState_should_return_string_html(): void
    {
        $expected = '<span class="badge badge-primary bg-orange-600">Nuevo</span>';

        $result = $this->valueObject->formatHtmlToState();
        $this->assertSame($expected, $result);;
        $this->assertIsString($result);
    }
}
