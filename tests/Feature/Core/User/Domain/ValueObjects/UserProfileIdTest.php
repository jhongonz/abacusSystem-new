<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Domain\ValueObjects;

use Core\User\Domain\ValueObjects\UserProfileId;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserProfileId::class)]
class UserProfileIdTest extends TestCase
{
    private UserProfileId $valueObject;
    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new UserProfileId(1);
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_int(): void
    {
        $expected = 1;
        $result = $this->valueObject->value();

        $this->assertSame($expected, $result);
        $this->assertIsInt($result);
    }

    public function test_value_should_return_null(): void
    {
        $this->valueObject = new UserProfileId();
        $result = $this->valueObject->value();

        $this->assertSame(null, $result);
        $this->assertNull($result);
    }

    public function test_setValue_should_change_value_and_return_object(): void
    {
        $expected = 2;
        $original = $this->valueObject->value();
        $object = $this->valueObject->setValue($expected);

        $return = $this->valueObject->value();
        $this->assertSame($expected, $return);
        $this->assertInstanceOf(UserProfileId::class, $object);
        $this->assertIsInt($return);
        $this->assertNotEquals($expected, $original);
    }
}
