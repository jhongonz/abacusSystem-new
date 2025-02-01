<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2025-01-06 20:00:53
 */

namespace Tests\Feature\Core\SharedContext\Model;

use Core\SharedContext\Model\ValueObjectStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(ValueObjectStatus::class)]
class ValueObjectStatusTest extends TestCase
{
    private ValueObjectStatus|MockObject $valueObject;

    protected function tearDown(): void
    {
        unset($this->valueObject);
        parent::tearDown();
    }

    /**
     * @throws \Exception
     */
    public function testConstructValueObjectCorrectly(): void
    {
        $this->valueObject = new ValueObjectStatus(1);

        $this->assertSame(1, $this->valueObject->value());
        $this->assertSame('<span class="badge badge-primary bg-orange-600">Nuevo</span>', $this->valueObject->formatHtmlToState());
    }

    /**
     * @throws \Exception
     */
    public function testConstructValueObjectIncorrectly(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('<Core\SharedContext\Model\ValueObjectStatus> does not allow the invalid state: <10>.');

        $this->valueObject = new ValueObjectStatus(10);
    }

    /**
     * @throws \Exception
     */
    public function testSetValueChangeValueCorrectly(): void
    {
        $this->valueObject = new ValueObjectStatus(1);
        $this->valueObject->setValue(2);

        $this->assertSame(2, $this->valueObject->value());
        $this->assertSame('<span class="badge badge-success">Activo</span>', $this->valueObject->formatHtmlToState());
    }

    /**
     * @throws \Exception
     */
    public function testSetValueChangeValueIncorrectly(): void
    {
        $this->valueObject = new ValueObjectStatus(1);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('<Core\SharedContext\Model\ValueObjectStatus> does not allow the invalid state: <12>.');

        $this->valueObject->setValue(12);
    }

    /**
     * @throws \Exception
     */
    public function testGetValueLiteralShouldReturnStringCorrectly(): void
    {
        $this->valueObject = new ValueObjectStatus(1);

        $this->assertIsString($this->valueObject->getValueLiteral());
        $this->assertSame('Nuevo', $this->valueObject->getValueLiteral());
    }

    /**
     * @throws \Exception
     */
    public function testGetValueLiteralShouldReturnStringCorrectlyWithToString(): void
    {
        $this->valueObject = new ValueObjectStatus(1);
        $result = (string) $this->valueObject;

        $this->assertIsString($result);
        $this->assertSame('Nuevo', $result);
    }

    /**
     * @throws \Exception
     */
    public function testActivateShouldChangeStateObjectCorrectly(): void
    {
        $this->valueObject = $this->getMockBuilder(ValueObjectStatus::class)
            ->onlyMethods(['changeValueLiteral'])
            ->setConstructorArgs([1])
            ->getMock();

        $this->valueObject->expects(self::once())
            ->method('changeValueLiteral')
            ->with(2)
            ->willReturnSelf();

        $this->valueObject->activate();

        $this->assertSame(2, $this->valueObject->value());
    }

    /**
     * @throws \Exception
     */
    public function testInactiveShouldChangeStateObjectCorrectly(): void
    {
        $this->valueObject = $this->getMockBuilder(ValueObjectStatus::class)
            ->onlyMethods(['changeValueLiteral'])
            ->setConstructorArgs([2])
            ->getMock();

        $this->valueObject->expects(self::once())
            ->method('changeValueLiteral')
            ->with(3)
            ->willReturnSelf();

        $this->valueObject->inactive();

        $this->assertSame(3, $this->valueObject->value());
    }

    /**
     * @throws \Exception
     */
    public function testIsNewShouldReturnBooleanCorrectly(): void
    {
        $this->valueObject = new ValueObjectStatus(1);

        $this->assertTrue($this->valueObject->isNew());
    }

    /**
     * @throws \Exception
     */
    public function testIsActivatedShouldReturnBooleanCorrectly(): void
    {
        $this->valueObject = new ValueObjectStatus(2);

        $this->assertTrue($this->valueObject->isActivated());
    }

    /**
     * @throws \Exception
     */
    public function testIsInactiveShouldReturnBooleanCorrectly(): void
    {
        $this->valueObject = new ValueObjectStatus(3);

        $this->assertTrue($this->valueObject->isInactivated());
    }

    public function testChangeValueLiteralShouldToBeProtected(): void
    {
        $reflection = new \ReflectionClass(ValueObjectStatus::class);
        $method = $reflection->getMethod('changeValueLiteral');

        $this->assertTrue($method->isProtected());
    }

    public function testValidateStateShouldToBeProtected(): void
    {
        $reflection = new \ReflectionClass(ValueObjectStatus::class);
        $method = $reflection->getMethod('validateState');

        $this->assertTrue($method->isProtected());
    }
}
