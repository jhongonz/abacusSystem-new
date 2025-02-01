<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Domain\ValueObjects;

use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserId::class)]
class UserIdTest extends TestCase
{
    private UserId $valueObject;

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
        $this->valueObject = new UserId(1);
        $result = $this->valueObject->value();

        $this->assertSame(1, $result);
        $this->assertIsInt($result);
    }

    public function testValueShouldReturnNull(): void
    {
        $this->valueObject = new UserId();
        $result = $this->valueObject->value();

        $this->assertSame(null, $result);
        $this->assertNull($result);
    }

    public function testSetValueShouldChangeValueAndReturnObject(): void
    {
        $this->valueObject = new UserId();

        $expected = 2;
        $original = $this->valueObject->value();
        $object = $this->valueObject->setValue($expected);

        $return = $this->valueObject->value();
        $this->assertSame($expected, $return);
        $this->assertInstanceOf(UserId::class, $object);
        $this->assertIsInt($return);
        $this->assertNotEquals($expected, $original);
    }

    public function testSetValueShouldReturnException(): void
    {
        $this->valueObject = new UserId();

        $expectedMessage = '<Core\User\Domain\ValueObjects\UserId> does not allow the value <0>.';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->valueObject->setValue(0);
    }

    public function testConstructValueShouldReturnException(): void
    {
        $expectedMessage = '<Core\User\Domain\ValueObjects\UserId> does not allow the value <0>.';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->valueObject = new UserId(0);
    }

    /**
     * @throws \ReflectionException
     */
    public function testValidateMinRange(): void
    {
        $this->valueObject = new UserId();

        $reflection = new \ReflectionClass(UserId::class);
        $method = $reflection->getMethod('validate');
        $this->assertTrue($method->isPrivate());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\User\Domain\ValueObjects\UserId> does not allow the value <-1>.');

        $method->invoke($this->valueObject, -1);
    }

    /**
     * @throws \ReflectionException
     */
    public function testValidateAllowsValidValues(): void
    {
        $this->valueObject = new UserId();

        $reflection = new \ReflectionClass(UserId::class);
        $method = $reflection->getMethod('validate');
        $this->assertTrue($method->isPrivate());

        $method->invoke($this->valueObject, 2);
    }
}
