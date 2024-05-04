<?php

namespace Tests\Feature\Core\Employee\Application\DataTransformer;

use Core\Employee\Application\DataTransformer\EmployeeDataTransformer;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(EmployeeDataTransformer::class)]
class EmployeesTest extends TestCase
{
    private Employee|MockObject $employee;
    private EmployeeDataTransformer $dataTransformer;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->employee = $this->createMock(Employee::class);
        $this->dataTransformer = new EmployeeDataTransformer();
    }

    public function tearDown(): void
    {
        unset(
            $this->employee,
            $this->dataTransformer
        );
        parent::tearDown();
    }

    public function test_write_should_change_and_return_self(): void
    {
        $result = $this->dataTransformer->write($this->employee);
        $this->assertInstanceOf(EmployeeDataTransformer::class, $result);
        $this->assertSame($result, $this->dataTransformer);
    }

    public function test_read_should_return_array_with_data(): void
    {
        $this->employee->expects(self::once())
            ->method('id');

        $this->employee->expects(self::once())
            ->method('userId');

        $this->employee->expects(self::once())
            ->method('identification');

        $this->employee->expects(self::once())
            ->method('identificationType');

        $this->employee->expects(self::once())
            ->method('name');

        $this->employee->expects(self::once())
            ->method('lastname');

        $this->employee->expects(self::once())
            ->method('phone');

        $this->employee->expects(self::once())
            ->method('email');

        $this->employee->expects(self::once())
            ->method('address');

        $this->employee->expects(self::once())
            ->method('birthdate');

        $this->employee->expects(self::once())
            ->method('birthdate');

        $this->employee->expects(self::once())
            ->method('observations');

        $this->employee->expects(self::once())
            ->method('image');

        $this->employee->expects(self::once())
            ->method('search');

        $this->employee->expects(self::once())
            ->method('state');

        $this->employee->expects(self::once())
            ->method('createdAt');

        $this->employee->expects(self::once())
            ->method('updatedAt');

        $this->dataTransformer->write($this->employee);
        $result = $this->dataTransformer->read();

        $this->assertIsArray($result);
        $this->assertArrayHasKey(Employee::TYPE,$result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function test_readToShare_should_return_array_with_data(): void
    {
        $this->employee->expects(self::once())
            ->method('id');

        $this->employee->expects(self::once())
            ->method('userId');

        $this->employee->expects(self::once())
            ->method('identification');

        $this->employee->expects(self::once())
            ->method('identificationType');

        $this->employee->expects(self::once())
            ->method('name');

        $this->employee->expects(self::once())
            ->method('lastname');

        $this->employee->expects(self::once())
            ->method('phone');

        $this->employee->expects(self::once())
            ->method('email');

        $this->employee->expects(self::once())
            ->method('address');

        $this->employee->expects(self::once())
            ->method('birthdate');

        $this->employee->expects(self::once())
            ->method('birthdate');

        $this->employee->expects(self::once())
            ->method('observations');

        $this->employee->expects(self::once())
            ->method('image');

        $this->employee->expects(self::once())
            ->method('search');

        $stateMock = $this->createMock(EmployeeState::class);
        $stateMock->expects(self::once())
            ->method('formatHtmlToState');

        $this->employee->expects(self::exactly(2))
            ->method('state')
            ->willReturn($stateMock);

        $this->employee->expects(self::once())
            ->method('createdAt');

        $this->employee->expects(self::once())
            ->method('updatedAt');

        $this->dataTransformer->write($this->employee);
        $result = $this->dataTransformer->readToShare();

        $this->assertIsArray($result);
        $this->assertArrayNotHasKey(Employee::TYPE,$result);
    }
}
