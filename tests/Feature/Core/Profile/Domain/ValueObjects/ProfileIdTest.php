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

    public function test_value_should_return_null(): void
    {
        $result = $this->valueObject->value();
        $this->assertNull($result);
    }

    public function test_setValue_should_return_self(): void
    {
        $result = $this->valueObject->setValue(1);

        $this->assertInstanceOf(ProfileId::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame(1, $result->value());
    }
}
