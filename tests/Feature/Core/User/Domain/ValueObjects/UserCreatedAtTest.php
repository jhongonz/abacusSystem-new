<?php

namespace Tests\Feature\Core\User\Domain\ValueObjects;

use Core\User\Domain\ValueObjects\UserCreatedAt;
use DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserCreatedAt::class)]
class UserCreatedAtTest extends TestCase
{
    private UserCreatedAt $valueObject;
    private DateTime $dateTime;

    public function setUp(): void
    {
        parent::setUp();
        $this->dateTime = new DateTime('2024-04-20 21:25:00');
        $this->valueObject = new UserCreatedAt($this->dateTime);
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

    public function test_setValue_should_change_value_and_return_object(): void
    {
        $expected = new DateTime('2024-04-20 21:27:00');
        $original = $this->valueObject->value();
        $object = $this->valueObject->setValue($expected);

        $return = $this->valueObject->value();
        $this->assertSame($expected, $return);
        $this->assertInstanceOf(UserCreatedAt::class, $object);
        $this->assertInstanceOf(DateTime::class, $return);
        $this->assertNotEquals($expected, $original);
    }
}
