<?php

namespace Tests\Feature\Core\Campus\Domain\ValueObjects;

use Core\Campus\Domain\ValueObjects\CampusInstitutionId;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CampusInstitutionId::class)]
class CampusInstitutionIdTest extends TestCase
{
    private CampusInstitutionId $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new CampusInstitutionId(1);
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_int(): void
    {
        $result = $this->valueObject->value();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function test_setValue_should_return_object(): void
    {
        $result = $this->valueObject->setValue(2);

        $this->assertInstanceOf(CampusInstitutionId::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(2, $result->value());
    }

    public function test_setValue_should_return_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusInstitutionId> does not allow the value <0>.');

        $this->valueObject->setValue(0);
    }
}
