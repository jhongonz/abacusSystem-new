<?php

namespace Tests\Feature\Core\Campus\Domain\ValueObjects;

use Core\Campus\Domain\ValueObjects\CampusName;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CampusName::class)]
class CampusNameTest extends TestCase
{
    private CampusName $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new CampusName('testing');
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_string(): void
    {
        $result = $this->valueObject->value();

        $this->assertIsString($result);
        $this->assertSame('testing', $result);
    }

    public function test_setValue_should_return_object(): void
    {
        $result = $this->valueObject->setValue('sandbox');

        $this->assertInstanceOf(CampusName::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame('sandbox', $result->value());
    }
}
