<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ProfileCreatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ProfileCreatedAt::class)]
class ProfileCreatedAtTest extends TestCase
{
    private ProfileCreatedAt $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ProfileCreatedAt;
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_datetime(): void
    {
        $result = $this->valueObject->value();
        $this->assertInstanceOf(\DateTime::class, $result);
    }

    public function test_setValue_should_return_self(): void
    {
        $datetime = new \DateTime('2024-05-05 09:49:00');
        $result = $this->valueObject->setValue($datetime);

        $this->assertInstanceOf(ProfileCreatedAt::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame($datetime, $result->value());
    }

    public function test___toString_should_return_string(): void
    {
        $dateTime = new \DateTime('2024-04-20 21:27:00');
        $result = $this->valueObject->setValue($dateTime);

        $this->assertInstanceOf(ProfileCreatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('2024-04-20 21:27:00', (string)$result);
    }
}
