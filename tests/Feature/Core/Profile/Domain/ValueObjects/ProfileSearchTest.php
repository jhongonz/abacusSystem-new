<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ProfileSearch;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ProfileSearch::class)]
class ProfileSearchTest extends TestCase
{
    private ProfileSearch $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ProfileSearch();
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
        $result = $this->valueObject->setValue('hello world');

        $this->assertInstanceOf(ProfileSearch::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame('hello world', $result->value());
        $this->assertIsString($result->value());
    }
}
