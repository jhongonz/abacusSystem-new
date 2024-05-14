<?php

namespace Core\Employee\Application\UseCases\CreateEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Exception;

class CreateEmployee extends UseCasesService
{
    public function __construct(EmployeeRepositoryContract $employeeRepository)
    {
        parent::__construct($employeeRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): Employee
    {
        $this->validateRequest($request, CreateEmployeeRequest::class);

        /** @var Employee $employee */
        $employee = $request->employee();
        $employee->refreshSearch();

        return $this->employeeRepository->persistEmployee($employee);
    }
}
