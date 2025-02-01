<?php

namespace Core\Employee\Infrastructure\Management;

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
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;

class EmployeeService implements EmployeeManagementContract
{
    public function __construct(
        private readonly EmployeeFactoryContract $employeeFactory,
        private readonly SearchEmployeeById $searchEmployeeById,
        private readonly SearchEmployeeByIdentification $searchEmployeeByIdentification,
        private readonly SearchEmployees $searchEmployees,
        private readonly UpdateEmployee $updateEmployee,
        private readonly CreateEmployee $createEmployee,
        private readonly DeleteEmployee $deleteEmployee,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function searchEmployeeById(?int $id): ?Employee
    {
        $request = new SearchEmployeeByIdRequest(
            $this->employeeFactory->buildEmployeeId($id)
        );

        return $this->searchEmployeeById->execute($request);
    }

    /**
     * @throws \Exception
     */
    public function searchEmployeeByIdentification(string $identification): ?Employee
    {
        $request = new SearchEmployeeByIdentificationRequest(
            $this->employeeFactory->buildEmployeeIdentification($identification)
        );

        return $this->searchEmployeeByIdentification->execute($request);
    }

    /**
     * @param array<string, mixed> $filters
     *
     * @throws \Exception
     */
    public function searchEmployees(array $filters = []): Employees
    {
        $request = new SearchEmployeesRequest($filters);

        /** @var Employees $employees */
        $employees = $this->searchEmployees->execute($request);

        foreach ($employees->aggregator() as $item) {
            /** @var Employee $employee */
            $employee = $this->searchEmployeeById($item);
            $employees->addItem($employee);
        }

        return $employees;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    public function updateEmployee(int $id, array $data): Employee
    {
        $employeeId = $this->employeeFactory->buildEmployeeId($id);
        $request = new UpdateEmployeeRequest($employeeId, $data);

        return $this->updateEmployee->execute($request);
    }

    /**
     * @param array<string ,mixed> $data
     *
     * @throws \Exception
     */
    public function createEmployee(array $data): Employee
    {
        $employee = $this->employeeFactory->buildEmployeeFromArray($data);
        $request = new CreateEmployeeRequest($employee);

        return $this->createEmployee->execute($request);
    }

    /**
     * @throws \Exception
     */
    public function deleteEmployee(int $id): void
    {
        $request = new DeleteEmployeeRequest(
            $this->employeeFactory->buildEmployeeId($id)
        );

        $this->deleteEmployee->execute($request);
    }
}
