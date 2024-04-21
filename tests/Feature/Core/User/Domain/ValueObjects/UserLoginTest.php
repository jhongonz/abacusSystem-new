<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Domain\ValueObjects;

use Core\User\Domain\ValueObjects\UserLogin;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserLogin::class)]
class UserLoginTest extends TestCase
{
    private UserLogin $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new UserLogin('user');
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_string(): void
    {
        $expected = 'user';
        $result = $this->valueObject->value();

        $this->assertSame($expected, $result);
        $this->assertIsString($result);
    }

    public function test_setValue_should_change_value_and_return_object(): void
    {
        $expected = 'user-change';
        $original = $this->valueObject->value();
        $object = $this->valueObject->setValue($expected);

        $return = $this->valueObject->value();
        $this->assertSame($expected, $return);
        $this->assertInstanceOf(UserLogin::class, $object);
        $this->assertIsString($return);
        $this->assertNotEquals($expected, $original);
    }
}
