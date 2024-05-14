<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ProfileName;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ProfileName::class)]
class ProfileNameTest extends TestCase
{
    private ProfileName $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ProfileName('test');
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_string(): void
    {
        $result = $this->valueObject->value();
        $this->assertIsString($result);
    }

    public function test_setValue_should_return_self(): void
    {
        $result = $this->valueObject->setValue('hello');

        $this->assertInstanceOf(ProfileName::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('hello', $result->value());
    }
}
