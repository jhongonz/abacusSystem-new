<?php

namespace Tests\Feature\Core\Institution\Domain\ValueObjects;

use Core\Institution\Domain\ValueObjects\InstitutionState;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(InstitutionState::class)]
class InstitutionStateTest extends TestCase
{
    private InstitutionState $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new InstitutionState;
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

    /**
     * @throws \Exception
     */
    public function test_setValue_should_return_self(): void
    {
        $result = $this->valueObject->setValue(2);

        $this->assertInstanceOf(InstitutionState::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(2, $result->value());
    }

    public function test_setValue_should_return_exception(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('<Core\Institution\Domain\ValueObjects\InstitutionState> does not allow the invalid state: <10>.');

        $this->valueObject->setValue(10);
    }
}
