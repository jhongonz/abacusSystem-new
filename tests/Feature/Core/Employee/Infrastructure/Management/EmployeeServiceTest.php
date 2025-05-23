<?php

namespace Tests\Feature\Core\Employee\Infrastructure\Management;

use Core\Employee\Application\UseCases\CreateEmployee\CreateEmployee;
use Core\Employee\Application\UseCases\CreateEmployee\CreateEmployeeRequest;
use Core\Employee\Application\UseCases\DeleteEmployee\DeleteEmployee;
use Core\Employee\Application\UseCases\DeleteEmployee\DeleteEmployeeRequest;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeById;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdentification;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdentificationRequest;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdRequest;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployees;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeesRequest;
use Core\Employee\Application\UseCases\UpdateEmployee\UpdateEmployee;
use Core\Employee\Application\UseCases\UpdateEmployee\UpdateEmployeeRequest;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Infrastructure\Management\EmployeeService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

#[CoversClass(EmployeeService::class)]
class EmployeeServiceTest extends TestCase
{
    private EmployeeFactoryContract|MockObject $factory;
    private SearchEmployeeById|MockObject $searchEmployeeById;
    private SearchEmployeeByIdentification|MockObject $searchEmployeeByIdentification;
    private SearchEmployees|MockObject $searchEmployees;
    private UpdateEmployee|MockObject $updateEmployee;
    private CreateEmployee|MockObject $createEmployee;
    private DeleteEmployee|MockObject $deleteEmployee;
    private EmployeeService $service;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(EmployeeFactoryContract::class);
        $this->searchEmployeeById = $this->createMock(SearchEmployeeById::class);
        $this->searchEmployeeByIdentification = $this->createMock(SearchEmployeeByIdentification::class);
        $this->searchEmployees = $this->createMock(SearchEmployees::class);
        $this->updateEmployee = $this->createMock(UpdateEmployee::class);
        $this->createEmployee = $this->createMock(CreateEmployee::class);
        $this->deleteEmployee = $this->createMock(DeleteEmployee::class);

        $this->service = new EmployeeService(
            $this->factory,
            $this->searchEmployeeById,
            $this->searchEmployeeByIdentification,
            $this->searchEmployees,
            $this->updateEmployee,
            $this->createEmployee,
            $this->deleteEmployee
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->factory,
            $this->searchEmployeeById,
            $this->searchEmployeeByIdentification,
            $this->searchEmployees,
            $this->updateEmployee,
            $this->createEmployee,
            $this->deleteEmployee,
            $this->service
        );
        parent::tearDown();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testSearchEmployeeByIdShouldReturnObject(): void
    {
        $employeeId = $this->createMock(EmployeeId::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(1)
            ->willReturn($employeeId);

        $request = new SearchEmployeeByIdRequest($employeeId);

        $employeeMock = $this->createMock(Employee::class);
        $this->searchEmployeeById->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($employeeMock);

        $result = $this->service->searchEmployeeById(1);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testSearchEmployeeByIdShouldReturnNull(): void
    {
        $employeeId = $this->createMock(EmployeeId::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(1)
            ->willReturn($employeeId);

        $request = new SearchEmployeeByIdRequest($employeeId);

        $this->searchEmployeeById->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn(null);

        $result = $this->service->searchEmployeeById(1);

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testSearchEmployeeByIdentificationShouldReturnObject(): void
    {
        $identification = $this->createMock(EmployeeIdentification::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeIdentification')
            ->with('test')
            ->willreturn($identification);

        $request = new SearchEmployeeByIdentificationRequest($identification);

        $employeeMock = $this->createMock(Employee::class);
        $this->searchEmployeeByIdentification->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($employeeMock);

        $result = $this->service->searchEmployeeByIdentification('test');

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($result, $employeeMock);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testSearchEmployeeByIdentificationShouldReturnNull(): void
    {
        $identification = $this->createMock(EmployeeIdentification::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeIdentification')
            ->with('test')
            ->willreturn($identification);

        $request = new SearchEmployeeByIdentificationRequest($identification);

        $this->searchEmployeeByIdentification->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn(null);

        $result = $this->service->searchEmployeeByIdentification('test');

        $this->assertNull($result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testUpdateEmployeeShouldReturnVoid(): void
    {
        $employeeId = $this->createMock(EmployeeId::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(1)
            ->willReturn($employeeId);

        $request = new UpdateEmployeeRequest($employeeId, []);

        $employeeMock = $this->createMock(Employee::class);
        $this->updateEmployee->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($employeeMock);

        $this->service->updateEmployee(1, []);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testCreateEmployeeShouldReturnVoid(): void
    {
        $employeeMock = $this->createMock(Employee::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeFromArray')
            ->with([])
            ->willReturn($employeeMock);

        $request = new CreateEmployeeRequest($employeeMock);

        $this->createEmployee->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($employeeMock);

        $result = $this->service->createEmployee([]);

        $this->assertInstanceOf(Employee::class, $result);
        $this->assertSame($employeeMock, $result);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testDeleteEmployeeShouldReturnVoid(): void
    {
        $employeeId = $this->createMock(EmployeeId::class);
        $this->factory->expects(self::once())
            ->method('buildEmployeeId')
            ->with(1)
            ->willReturn($employeeId);

        $request = new DeleteEmployeeRequest($employeeId);

        $this->deleteEmployee->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn(null);

        $this->service->deleteEmployee(1);
        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testSearchEmployeesShouldReturnEmployees(): void
    {
        $request = new SearchEmployeesRequest([]);

        $employees = $this->createMock(Employees::class);
        $employees->expects(self::once())
            ->method('aggregator')
            ->willReturn([1]);

        $this->searchEmployees->expects(self::once())
            ->method('execute')
            ->with($request)
            ->willReturn($employees);

        $employeeMock = $this->createMock(Employee::class);
        $this->searchEmployeeById->expects(self::once())
            ->method('execute')
            ->willReturn($employeeMock);

        $employees->expects(self::once())
            ->method('addItem')
            ->with($employeeMock)
            ->willReturnSelf();

        $result = $this->service->searchEmployees();

        $this->assertInstanceOf(Employees::class, $result);
    }
}
