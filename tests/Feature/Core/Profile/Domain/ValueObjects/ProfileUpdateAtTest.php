<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ProfileUpdatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ProfileUpdatedAt::class)]
class ProfileUpdateAtTest extends TestCase
{
    private ProfileUpdatedAt $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ProfileUpdatedAt;
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
        $datetime = new \DateTime('2024-05-05 22:20:00');
        $result = $this->valueObject->setValue($datetime);

        $this->assertInstanceOf(ProfileUpdatedAt::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertInstanceOf(\DateTime::class, $result->value());
        $this->assertSame($datetime, $result->value());
    }
}
