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
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnInt(): void
    {
        $this->valueObject = new UserProfileId(1);
        $result = $this->valueObject->value();

        $this->assertSame(1, $result);
        $this->assertIsInt($result);
    }

    public function testValueShouldReturnNull(): void
    {
        $this->valueObject = new UserProfileId();
        $result = $this->valueObject->value();

        $this->assertSame(null, $result);
        $this->assertNull($result);
    }

    public function testSetValueShouldChangeValueAndReturnObject(): void
    {
        $this->valueObject = new UserProfileId(10);

        $expected = 2;
        $original = $this->valueObject->value();
        $object = $this->valueObject->setValue($expected);

        $return = $this->valueObject->value();

        $this->assertSame($expected, $return);
        $this->assertInstanceOf(UserProfileId::class, $object);
        $this->assertIsInt($return);
        $this->assertNotEquals($expected, $original);
    }

    public function testSetValueShouldReturnException(): void
    {
        $this->valueObject = new UserProfileId();

        $expectedMessage = '<Core\User\Domain\ValueObjects\UserProfileId> does not allow the value <0>.';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->valueObject->setValue(0);
    }

    public function testConstructValueShouldReturnException(): void
    {
        $expectedMessage = '<Core\User\Domain\ValueObjects\UserProfileId> does not allow the value <0>.';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->valueObject = new UserProfileId(0);
    }

    /**
     * @throws \ReflectionException
     */
    public function testValidateMinRange(): void
    {
        $this->valueObject = new UserProfileId();

        $reflection = new \ReflectionClass(UserProfileId::class);
        $method = $reflection->getMethod('validate');
        $this->assertTrue($method->isPrivate());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\User\Domain\ValueObjects\UserProfileId> does not allow the value <-1>.');

        $method->invoke($this->valueObject, -1);
    }

    /**
     * @throws \ReflectionException
     */
    public function testValidateAllowsValidValues(): void
    {
        $this->valueObject = new UserProfileId();

        $reflection = new \ReflectionClass(UserProfileId::class);
        $method = $reflection->getMethod('validate');
        $this->assertTrue($method->isPrivate());

        $method->invoke($this->valueObject, 2);
    }
}
