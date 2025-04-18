<?php

namespace Core\Employee\Application\UseCases\CreateEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;

class CreateEmployee extends UseCasesService
{
    public function __construct(EmployeeRepositoryContract $employeeRepository)
    {
        parent::__construct($employeeRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): Employee
    {
        $this->validateRequest($request, CreateEmployeeRequest::class);

        /** @var CreateEmployeeRequest $request */
        $employee = $request->employee();
        $employee->refreshSearch();

        return $this->employeeRepository->persistEmployee($employee);
    }
}
