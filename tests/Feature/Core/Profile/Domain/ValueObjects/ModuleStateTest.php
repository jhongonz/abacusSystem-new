<?php

namespace Tests\Feature\Core\Profile\Domain\ValueObjects;

use Core\Profile\Domain\ValueObjects\ModuleState;
use Core\SharedContext\Model\ValueObjectStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ModuleState::class)]
#[CoversClass(ValueObjectStatus::class)]
class ModuleStateTest extends TestCase
{
    private ModuleState $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new ModuleState;
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

        $this->assertInstanceOf(ModuleState::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame(2, $result->value());
    }

    public function test_setValue_should_return_exception(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('<Core\Profile\Domain\ValueObjects\ModuleState> does not allow the invalid state: <10>.');

        $this->valueObject->setValue(10);
    }

    public function test_value_literal_should_return_with__string(): void
    {
        $result = $this->valueObject->__toString();

        $this->assertSame('Nuevo', $result);
        $this->assertIsString($result);
    }
}
