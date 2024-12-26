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
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnNull(): void
    {
        $this->valueObject = new CampusId();
        $result = $this->valueObject->value();

        $this->assertNull($result);
    }

    public function testSetValueShouldReturnObject(): void
    {
        $this->valueObject = new CampusId();

        $this->valueObject->setValue(1);
        $this->assertSame(1, $this->valueObject->value());

        $this->valueObject->setValue(10);
        $this->assertSame(10, $this->valueObject->value());
    }

    public function testValueShouldReturnInt(): void
    {
        $this->valueObject = new CampusId(10);
        $result = $this->valueObject->value();

        $this->assertIsInt($result);
        $this->assertSame(10, $result);
    }

    public function testSetValueShouldReturnException(): void
    {
        $this->valueObject = new CampusId();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusId> does not allow the value <0>.');

        $this->valueObject->setValue(0);
    }

    public function testConstructValueShouldReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusId> does not allow the value <0>.');

        $this->valueObject = new CampusId(0);
    }

    /**
     * @throws \ReflectionException
     */
    public function testValidateMinRange(): void
    {
        $this->valueObject = new CampusId();

        $reflection = new \ReflectionClass(CampusId::class);
        $method = $reflection->getMethod('validate');
        $this->assertTrue($method->isPrivate());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusId> does not allow the value <-1>.');

        $method->invoke($this->valueObject, -1);
    }

    /**
     * @throws \ReflectionException
     */
    public function testValidateAllowsValidValues(): void
    {
        $this->valueObject = new CampusId();

        $reflection = new \ReflectionClass(CampusId::class);
        $method = $reflection->getMethod('validate');
        $this->assertTrue($method->isPrivate());

        $method->invoke($this->valueObject, '2');
    }
}
