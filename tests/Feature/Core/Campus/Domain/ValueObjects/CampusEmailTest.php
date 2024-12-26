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
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function testValueShouldReturnNull(): void
    {
        $this->valueObject = new CampusEmail();

        $result = $this->valueObject->value();
        $this->assertNull($result);
    }

    public function testSetValueShouldReturnObject(): void
    {
        $this->valueObject = new CampusEmail();
        $this->valueObject->setValue('test@test.com');
        $this->assertSame('test@test.com', $this->valueObject->value());

        $this->valueObject->setValue('test2@test.com');
        $this->assertSame('test2@test.com', $this->valueObject->value());

        $this->valueObject->setValue();
        $this->assertNull($this->valueObject->value());
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

    public function testValueShouldReturnExceptionWhenSetValueIsNotValid(): void
    {
        $this->valueObject = new CampusEmail();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusEmail> does not allow the invalid email: <testing>.');

        $this->valueObject->setValue('testing');
    }
}
