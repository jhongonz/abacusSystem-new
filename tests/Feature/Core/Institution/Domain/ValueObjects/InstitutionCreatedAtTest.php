<?php

namespace Tests\Feature\Core\Institution\Domain\ValueObjects;

use Core\Institution\Domain\ValueObjects\InstitutionCreatedAt;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(InstitutionCreatedAt::class)]
class InstitutionCreatedAtTest extends TestCase
{
    private InstitutionCreatedAt $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new InstitutionCreatedAt;
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_object(): void
    {
        $result = $this->valueObject->value();
        $this->assertInstanceOf(\DateTime::class, $result);
    }

    public function test_setValue_should_return_self(): void
    {
        $datetime = new \DateTime('2024-05-19 18:05:00');
        $result = $this->valueObject->setValue($datetime);

        $this->assertInstanceOf(InstitutionCreatedAt::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame($datetime, $this->valueObject->value());
    }
}
