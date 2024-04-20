<?php

namespace Tests\Feature\Core\User\Domain\ValueObjects;

use Core\User\Domain\ValueObjects\UserEmployeeId;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use InvalidArgumentException;
use Tests\TestCase;

class UserEmployeeIdTest extends TestCase
{
    private UserEmployeeId $valueObject;

    public  function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new UserEmployeeId(1);
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
        $this->valueObject = new UserEmployeeId();
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
        $this->assertInstanceOf(UserEmployeeId::class, $object);
        $this->assertIsInt($return);
        $this->assertNotEquals($expected, $original);
    }

    public function test_setValue_should_return_exception(): void
    {
        $expectedMessage = '<Core\User\Domain\ValueObjects\UserEmployeeId> does not allow the value <0>.';

        try {
            $valueObject = new UserEmployeeId(0);
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame($expectedMessage, $exception->getMessage());
        }
    }
}
