<?php

namespace Core\Employee\Infrastructure\Management;

use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeById;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdRequest;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdentification;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeeByIdentificationRequest;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployees;
use Core\Employee\Application\UseCases\SearchEmployee\SearchEmployeesRequest;
use Core\Employee\Domain\Contracts\EmployeeManagementContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Exception;

class EmployeeService implements EmployeeManagementContract
{
    private SearchEmployeeById $searchEmployeeById;
    private SearchEmployeeByIdentification $searchEmployeeByIdentification;
    private SearchEmployees $searchEmployees;

    public function __construct(
        SearchEmployeeById $searchEmployeeById,
        SearchEmployeeByIdentification $searchEmployeeByIdentification,
        SearchEmployees $searchEmployees,
    ) {
        $this->searchEmployeeById = $searchEmployeeById;
        $this->searchEmployeeByIdentification = $searchEmployeeByIdentification;
        $this->searchEmployees = $searchEmployees;
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

    public function searchEmployees(array $filters = []): Employees
    {
        $request = new SearchEmployeesRequest($filters);

        return $this->searchEmployees->execute($request);
    }
}
