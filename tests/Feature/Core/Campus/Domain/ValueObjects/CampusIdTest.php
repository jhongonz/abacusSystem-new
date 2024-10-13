<?php

namespace Tests\Feature\Core\Campus\Domain\ValueObjects;

use Core\Campus\Domain\ValueObjects\CampusId;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CampusId::class)]
class CampusIdTest extends TestCase
{
    private CampusId $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new CampusId;
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
        $result = $this->valueObject->setValue(1);

        $this->assertInstanceOf(CampusId::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(1, $result->value());
    }

    public function test_value_should_return_int(): void
    {
        $this->valueObject = new CampusId(1);
        $result = $this->valueObject->value();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function test_setValue_should_return_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusId> does not allow the value <0>.');

        $this->valueObject->setValue(0);
    }
}
