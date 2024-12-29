<?php

namespace Tests\Feature\Core\Employee\Domain;

use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\SharedContext\Model\ArrayIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

#[CoversClass(ArrayIterator::class)]
#[CoversClass(Employees::class)]
class EmployeesTest extends TestCase
{
    private Employees $employees;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->employees);
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testConstructValueCollectionCorrectly(): void
    {
        $employeeMock = $this->createMock(Employee::class);
        $employeeMock2 = $this->createMock(Employee::class);

        $this->employees = new Employees([$employeeMock, $employeeMock2]);

        $this->assertCount(2, $this->employees->items());
        $this->assertSame([$employeeMock, $employeeMock2], $this->employees->items());
    }

    public function testConstructValueCollectionShouldReturnException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Item is not valid to collection Core\Employee\Domain\Employees');

        $this->employees = new Employees(['testing']);
    }

    /**
     * @throws Exception
     */
    public function testAddItemShouldAddAndReturnSelf(): void
    {
        $this->employees = new Employees();

        $employeeMock = $this->createMock(Employee::class);
        $result = $this->employees->addItem($employeeMock);

        $this->assertInstanceOf(Employees::class, $result);
        $this->assertSame($result, $this->employees);
        $this->assertSame([$employeeMock], $result->items());
    }

    /**
     * @throws Exception
     */
    public function testItemsShouldReturnArray(): void
    {
        $employeeMock = $this->createMock(Employee::class);
        $this->employees = new Employees([$employeeMock]);

        $result = $this->employees->items();
        $this->assertIsArray($result);
        $this->assertSame([$employeeMock], $result);
    }

    public function testAddIdShouldAddAndReturnSelf(): void
    {
        $this->employees = new Employees();

        $result = $this->employees->addId(1);

        $this->assertInstanceOf(Employees::class, $result);
        $this->assertSame($result, $this->employees);
        $this->assertSame([1], $this->employees->aggregator());
    }

    public function testAggregatorShouldReturnArray(): void
    {
        $this->employees = new Employees();

        $result = $this->employees->aggregator();
        $this->assertIsArray($result);
    }

    public function testFiltersShouldReturnArray(): void
    {
        $this->employees = new Employees();

        $result = $this->employees->filters();
        $this->assertIsArray($result);
    }

    public function testSetFiltersShouldChangeAndReturnSelf(): void
    {
        $this->employees = new Employees();
        $result = $this->employees->setFilters(['hello']);

        $this->assertInstanceOf(Employees::class, $result);
        $this->assertSame($result, $this->employees);
        $this->assertSame(['hello'], $this->employees->filters());
    }
}
