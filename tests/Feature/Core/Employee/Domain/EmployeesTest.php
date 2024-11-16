<?php

namespace Tests\Feature\Core\Employee\Domain;

use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(Employees::class)]
class EmployeesTest extends TestCase
{
    private Employee|MockObject $employee;

    private Employees $employees;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employee = $this->createMock(Employee::class);
        $this->employees = new Employees([$this->employee]);
    }

    public function tearDown(): void
    {
        unset(
            $this->employee,
            $this->employees
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testAddItemShouldAddAndReturnSelf(): void
    {
        $employeeMock = $this->createMock(Employee::class);
        $result = $this->employees->addItem($employeeMock);

        $this->assertInstanceOf(Employees::class, $result);
        $this->assertSame($result, $this->employees);
    }

    public function testItemsShouldReturnArray(): void
    {
        $result = $this->employees->items();
        $this->assertIsArray($result);
    }

    public function testAddIdShouldAddAndReturnSelf(): void
    {
        $result = $this->employees->addId(1);
        $this->assertInstanceOf(Employees::class, $result);
        $this->assertSame($result, $this->employees);
    }

    public function testAggregatorShouldReturnArray(): void
    {
        $result = $this->employees->aggregator();
        $this->assertIsArray($result);
    }

    public function testFiltersShouldReturnArray(): void
    {
        $result = $this->employees->filters();
        $this->assertIsArray($result);
    }

    public function testSetFiltersShouldChangeAndReturnSelf(): void
    {
        $result = $this->employees->setFilters(['hello']);
        $this->assertInstanceOf(Employees::class, $result);
        $this->assertSame($result, $this->employees);
        $this->assertSame(['hello'], $this->employees->filters());
    }
}
