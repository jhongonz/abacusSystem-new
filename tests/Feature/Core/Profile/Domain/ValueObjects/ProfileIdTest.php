<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ProfileId;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ProfileId::class)]
class ProfileIdTest extends TestCase
{
    private ProfileId $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ProfileId();
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnNull(): void
    {
        $result = $this->valueObject->value();
        $this->assertNull($result);
    }

    public function testSetValueShouldReturnSelf(): void
    {
        $result = $this->valueObject->setValue(1);

        $this->assertInstanceOf(ProfileId::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame(1, $result->value());
    }

    public function testValueShouldReturnInt(): void
    {
        $this->valueObject = new ProfileId(2);
        $result = $this->valueObject->value();

        $this->assertIsInt($result);
        $this->assertSame($result, 2);
    }

    public function testSetValueShouldReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Profile\Domain\ValueObjects\ProfileId> does not allow the value <0>.');

        $this->valueObject->setValue(0);
    }

    public function testConstructShouldReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Profile\Domain\ValueObjects\ProfileId> does not allow the value <0>.');

        $this->valueObject = new ProfileId(0);
    }
}
