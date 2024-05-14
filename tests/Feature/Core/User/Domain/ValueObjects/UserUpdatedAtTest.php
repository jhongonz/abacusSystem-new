<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Domain\ValueObjects;

use Core\User\Domain\ValueObjects\UserUpdatedAt;
use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserUpdatedAt::class)]
class UserUpdatedAtTest extends TestCase
{
    private UserUpdatedAt $valueObject;

    private DateTime $dateTime;

    public function setUp(): void
    {
        parent::setUp();
        $this->dateTime = new DateTime('2024-04-20 21:25:00');
        $this->valueObject = new UserUpdatedAt($this->dateTime);
    }

    public function tearDown(): void
    {
        unset($this->valueObject, $this->dateTime);
        parent::tearDown();
    }

    public function test_value_should_return_datetime(): void
    {
        $result = $this->valueObject->value();

        $this->assertSame($this->dateTime, $result);
        $this->assertInstanceOf(DateTime::class, $result);
    }

    public function test_value_should_return_null(): void
    {
        $this->valueObject = new UserUpdatedAt;
        $result = $this->valueObject->value();

        $this->assertSame(null, $result);
        $this->assertNull($result);
    }

    public function test_setValue_should_change_value_and_return_object(): void
    {
        $expected = new DateTime('2024-04-20 21:27:00');
        $original = $this->valueObject->value();
        $object = $this->valueObject->setValue($expected);

        $return = $this->valueObject->value();
        $this->assertSame($expected, $return);
        $this->assertInstanceOf(UserUpdatedAt::class, $object);
        $this->assertInstanceOf(DateTime::class, $return);
        $this->assertNotEquals($expected, $original);
    }
}
