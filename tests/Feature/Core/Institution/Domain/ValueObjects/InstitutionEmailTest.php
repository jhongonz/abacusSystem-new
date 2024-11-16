<?php

namespace Tests\Feature\Core\Institution\Domain\ValueObjects;

use Core\Institution\Domain\ValueObjects\InstitutionEmail;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(InstitutionEmail::class)]
class InstitutionEmailTest extends TestCase
{
    private InstitutionEmail $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new InstitutionEmail();
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

    public function testValueShouldReturnString(): void
    {
        $this->valueObject = new InstitutionEmail('testing@test.com');

        $result = $this->valueObject->value();
        $this->assertIsString($result);
        $this->assertSame('testing@test.com', $result);
    }

    public function testSetValueShouldReturnSelf(): void
    {
        $result = $this->valueObject->setValue('test@test.com');

        $this->assertInstanceOf(InstitutionEmail::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('test@test.com', $result->value());
    }

    public function testSetValueShouldReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<Core\Institution\Domain\ValueObjects\InstitutionEmail> does not allow the invalid email: <testing>.');

        $this->valueObject->setValue('testing');
    }
}
