<?php

namespace Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Exception;

class SearchEmployees extends UseCasesService
{
    public function __construct(EmployeeRepositoryContract $employeeRepository)
    {
        parent::__construct($employeeRepository);
    }

    /**
     * @throws Exception
     */
    public function execute(RequestService $request): null|Employee|Employees
    {
        $this->validateRequest($request, SearchEmployeesRequest::class);

        return $this->employeeRepository->getAll($request->filters());
    }
}
