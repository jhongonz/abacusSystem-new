<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Tests\Feature\Core\User\Domain\ValueObjects;

use Core\User\Domain\ValueObjects\UserPhoto;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserPhoto::class)]
class UserPhotoTest extends TestCase
{
    private UserPhoto $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new UserPhoto('test.jpg');
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnString(): void
    {
        $expected = 'test.jpg';
        $result = $this->valueObject->value();

        $this->assertSame($expected, $result);
        $this->assertIsString($result);
    }

    public function testSetValueShouldChangeValueAndReturnObject(): void
    {
        $expected = 'test_change.jpg';
        $original = $this->valueObject->value();
        $object = $this->valueObject->setValue($expected);

        $return = $this->valueObject->value();
        $this->assertSame($expected, $return);
        $this->assertInstanceOf(UserPhoto::class, $object);
        $this->assertIsString($return);
        $this->assertNotEquals($expected, $original);
    }
}
