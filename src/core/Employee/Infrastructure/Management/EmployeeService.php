<?php

namespace Core\Employee\Infrastructure\Management;

use Core\Employee\Application\UseCases\CreateEmployee\CreateEmployee;
use Core\Employee\Application\UseCases\CreateEmployee\CreateEmployeeRequest;
use Core\Employee\Application\UseCases\DeleteEmployee\DeleteEmployee;
use Core\Employee\Application\UseCases\DeleteEmployee\DeleteEmployeeRequest;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeById;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdRequest;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdentification;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdentificationRequest;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployees;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeesRequest;
use Core\Employee\Application\UseCases\UpdateEmployee\UpdateEmployee;
use Core\Employee\Application\UseCases\UpdateEmployee\UpdateEmployeeRequest;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Exception;

class EmployeeService implements EmployeeManagementContract
{
    private EmployeeFactoryContract $employeeFactory;
    private SearchEmployeeById $searchEmployeeById;
    private SearchEmployeeByIdentification $searchEmployeeByIdentification;
    private SearchEmployees $searchEmployees;
    private UpdateEmployee $updateEmployee;
    private CreateEmployee $createEmployee;
    private DeleteEmployee $deleteEmployee;

    public function __construct(
        EmployeeFactoryContract $employeeFactory,
        SearchEmployeeById $searchEmployeeById,
        SearchEmployeeByIdentification $searchEmployeeByIdentification,
        SearchEmployees $searchEmployees,
        UpdateEmployee $updateEmployee,
        CreateEmployee $createEmployee,
        DeleteEmployee $deleteEmployee,
    ) {
        $this->employeeFactory = $employeeFactory;
        $this->searchEmployeeById = $searchEmployeeById;
        $this->searchEmployeeByIdentification = $searchEmployeeByIdentification;
        $this->searchEmployees = $searchEmployees;
        $this->updateEmployee = $updateEmployee;
        $this->createEmployee = $createEmployee;
        $this->deleteEmployee = $deleteEmployee;
    }

    /**
     * @throws Exception
     */
    public function searchEmployeeById(EmployeeId $id): null|Employee
    {
        $request = new SearchEmployeeByIdRequest($id);

        return $this->searchEmployeeById->execute($request);
    }

    /**
     * @throws Exception
     */
    public function searchEmployeeByIdentification(EmployeeIdentification $identification): null|Employee
    {
        $request = new SearchEmployeeByIdentificationRequest($identification);

        return $this->searchEmployeeByIdentification->execute($request);
    }

    /**
     * @throws Exception
     */
    public function searchEmployees(array $filters = []): Employees
    {
        $request = new SearchEmployeesRequest($filters);
        $employees = $this->searchEmployees->execute($request);
        foreach ($employees->aggregator() as $item) {
            $employee = $this->searchEmployeeById($this->employeeFactory->buildEmployeeId($item));
            $employees->addItem($employee);
        }

        return $employees;
    }

    /**
     * @throws Exception
     */
    public function updateEmployee(EmployeeId $id, array $data): void
    {
        $request = new UpdateEmployeeRequest($id, $data);

        $this->updateEmployee->execute($request);
    }

    /**
     * @throws Exception
     */
    public function createEmployee(Employee $employee): void
    {
        $request = new CreateEmployeeRequest($employee);

        $this->createEmployee->execute($request);
    }

    /**
     * @throws Exception
     */
    public function deleteEmployee(EmployeeId $id): void
    {
        $request = new DeleteEmployeeRequest($id);

        $this->deleteEmployee->execute($request);
    }
}
