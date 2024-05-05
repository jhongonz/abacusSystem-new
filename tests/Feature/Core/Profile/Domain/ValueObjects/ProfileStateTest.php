<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ProfileState;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ProfileState::class)]
class ProfileStateTest extends TestCase
{
    private ProfileState $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ProfileState();
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_int(): void
    {
        $result = $this->valueObject->value();
        $this->assertIsInt($result);
    }

    /**
     * @throws \Exception
     */
    public function test_setValue_should_return_self(): void
    {
        $result = $this->valueObject->setValue(2);

        $this->assertInstanceOf(ProfileState::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame(2, $result->value());
    }

    public function test_setValue_should_return_exception(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('<Core\Profile\Domain\ValueObjects\ProfileState> does not allow the invalid state: <10>.');

        $this->valueObject->setValue(10);
    }
}
