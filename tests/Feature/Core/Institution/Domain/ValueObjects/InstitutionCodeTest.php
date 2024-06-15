<?php

namespace Tests\Feature\Core\Institution\Domain\ValueObjects;

use Core\Institution\Domain\ValueObjects\InstitutionCode;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(InstitutionCode::class)]
class InstitutionCodeTest extends TestCase
{
    private InstitutionCode $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new InstitutionCode;
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
        $result = $this->valueObject->setValue('testing');

        $this->assertInstanceOf(InstitutionCode::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('testing', $result->value());
    }
}
