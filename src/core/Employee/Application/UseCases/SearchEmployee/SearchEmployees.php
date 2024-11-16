<?php

namespace Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employees;

class SearchEmployees extends UseCasesService
{
    public function __construct(EmployeeRepositoryContract $employeeRepository)
    {
        parent::__construct($employeeRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): ?Employees
    {
        $this->validateRequest($request, SearchEmployeesRequest::class);

        /* @var SearchEmployeesRequest $request */
        return $this->employeeRepository->getAll($request->filters());
    }
}
