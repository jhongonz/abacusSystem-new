<?php

namespace Tests\Feature\Core\Campus\Domain\ValueObjects;

use Core\Campus\Domain\ValueObjects\CampusAddress;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CampusAddress::class)]
class CampusAddressTest extends TestCase
{
    private CampusAddress $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new CampusAddress;
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

    public function test_setValue_should_return_object(): void
    {
        $result = $this->valueObject->setValue('testing');

        $this->assertInstanceOf(CampusAddress::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('testing', $result->value());
    }

    public function test_value_should_return_string(): void
    {
        $this->valueObject = new CampusAddress('testing');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('testing', $result);
    }
}
