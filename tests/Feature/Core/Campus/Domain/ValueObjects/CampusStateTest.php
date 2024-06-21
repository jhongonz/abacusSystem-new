<?php

namespace Tests\Feature\Core\Campus\Domain\ValueObjects;

use Core\Campus\Domain\ValueObjects\CampusState;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CampusState::class)]
class CampusStateTest extends TestCase
{
    private CampusState $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new CampusState;
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
    public function test_setValue_should_change_and_return_object(): void
    {
        $result = $this->valueObject->setValue(2);

        $this->assertInstanceOf(CampusState::class, $result);
        $this->assertSame($this->valueObject, $result);
        $this->assertSame(2, $result->value());
    }

    public function test_setValue_should_return_exception(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('<Core\Campus\Domain\ValueObjects\CampusState> does not allow the invalid state: <10>.');

        $this->valueObject->setValue(10);
    }
}
