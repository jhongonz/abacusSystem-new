<?php

namespace Tests\Feature\Core\Campus\Domain\ValueObjects;

use Core\Campus\Domain\ValueObjects\CampusEmail;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CampusEmail::class)]
class CampusEmailTest extends TestCase
{
    private CampusEmail $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new CampusEmail();
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnNull(): void
    {
        $result = $this->valueObject->value();
        $this->assertNull($result);
    }

    public function testSetValueShouldReturnObject(): void
    {
        $result = $this->valueObject->setValue('test@test.com');

        $this->assertInstanceOf(CampusEmail::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('test@test.com', $result->value());
    }

    public function testValueShouldReturnString(): void
    {
        $this->valueObject = new CampusEmail('test@test.com');
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('test@test.com', $result);
    }

    public function testValueShouldReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusEmail> does not allow the invalid email: <testing>.');

        $this->valueObject = new CampusEmail('testing');
    }
}
