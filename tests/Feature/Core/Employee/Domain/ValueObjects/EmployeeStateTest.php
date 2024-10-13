<?php

namespace Tests\Feature\Core\Employee\Domain\ValueObjects;

use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\SharedContext\Model\ValueObjectStatus;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmployeeState::class)]
#[CoversClass(ValueObjectStatus::class)]
class EmployeeStateTest extends TestCase
{
    private EmployeeState $valueObject;

    public function setUp(): void
    {
        parent::setUp();
        $this->valueObject = new EmployeeState;
    }

    public function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    public function test_value_should_return_int_and_new_state(): void
    {
        $result = $this->valueObject->value();

        $this->assertIsInt($result);
        $this->assertSame(ValueObjectStatus::STATE_NEW, $result);
    }

    /**
     * @throws Exception
     */
    public function test_setValue_should_change_and_return_self(): void
    {
        $result = $this->valueObject->setValue(2);

        $this->assertInstanceOf(EmployeeState::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame(2, $this->valueObject->value());
    }

    public function test_setValue_should_return_exception(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('<Core\Employee\Domain\ValueObjects\EmployeeState> does not allow the invalid state: <10>.');

        $this->valueObject->setValue(10);
    }

    public function test_getValueLiteral_should_return_string(): void
    {
        $result = $this->valueObject->getValueLiteral();

        $this->assertIsString($result);
        $this->assertSame('Nuevo', $result);
    }

    public function test_value_literal_should_return_with__string(): void
    {
        $result = $this->valueObject->__toString();

        $this->assertSame('Nuevo', $result);
        $this->assertIsString($result);
    }

    public function test_activate_should_change_activated_and_return_self(): void
    {
        $result = $this->valueObject->activate();

        $this->assertInstanceOf(EmployeeState::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame(ValueObjectStatus::STATE_ACTIVE, $this->valueObject->value());
    }

    public function test_activate_should_change_inactivated_and_return_self(): void
    {
        $result = $this->valueObject->inactive();

        $this->assertInstanceOf(EmployeeState::class, $result);
        $this->assertSame($result, $this->valueObject);
        $this->assertSame(ValueObjectStatus::STATE_INACTIVE, $this->valueObject->value());
    }

    public function test_isNew_should_return_boolean(): void
    {
        $result = $this->valueObject->isNew();
        $this->assertIsBool($result);
    }

    public function test_isActivated_should_return_boolean(): void
    {
        $result = $this->valueObject->isActivated();
        $this->assertIsBool($result);
    }

    public function test_isInactivated_should_return_boolean(): void
    {
        $result = $this->valueObject->isInactivated();
        $this->assertIsBool($result);
    }

    public function test_formatHtmlToState_should_return_string(): void
    {
        $result = $this->valueObject->formatHtmlToState();
        $this->assertIsString($result);
    }
}
