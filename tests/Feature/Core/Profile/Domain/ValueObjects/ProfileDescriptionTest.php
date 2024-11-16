<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ProfileDescription;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ProfileDescription::class)]
class ProfileDescriptionTest extends TestCase
{
    private ProfileDescription $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ProfileDescription();
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
        $result = $this->valueObject->setValue('testing');

        $this->assertInstanceOf(ProfileDescription::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('testing', $result->value());
    }
}
