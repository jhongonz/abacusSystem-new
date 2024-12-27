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
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnInt(): void
    {
        $this->valueObject = new CampusInstitutionId(1);
        $result = $this->valueObject->value();

        $this->assertIsInt($result);
        $this->assertSame(1, $result);
    }

    public function testSetValueShouldReturnObject(): void
    {
        $this->valueObject = new CampusInstitutionId(1);

        $this->valueObject->setValue(2);
        $this->assertSame(2, $this->valueObject->value());

        $this->valueObject->setValue(20);
        $this->assertSame(20, $this->valueObject->value());
    }

    public function testSetValueShouldReturnException(): void
    {
        $this->valueObject = new CampusInstitutionId(1);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusInstitutionId> does not allow the value <0>.');

        $this->valueObject->setValue(0);
    }

    public function testSetValueShouldReturnExceptionWhenConstructValueError(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusInstitutionId> does not allow the value <0>.');

        $this->valueObject = new CampusInstitutionId(0);
    }
}
