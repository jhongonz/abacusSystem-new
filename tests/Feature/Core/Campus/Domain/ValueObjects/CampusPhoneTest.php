<?php

namespace Tests\Feature\Core\Campus\Domain\ValueObjects;

use Core\Campus\Domain\ValueObjects\CampusPhone;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CampusPhone::class)]
class CampusPhoneTest extends TestCase
{
    private CampusPhone $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new CampusPhone;
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

    public function test_value_should_return_string(): void
    {
        $this->valueObject->setValue('testing');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('testing', $result);
    }

    public function test_setValue_should_return_object(): void
    {
        $result = $this->valueObject->setValue('sandbox');

        $this->assertInstanceOf(CampusPhone::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('sandbox', $result->value());
    }
}
