<?php

namespace Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;

class SearchEmployeeById extends UseCasesService
{
    public function __construct(EmployeeRepositoryContract $employeeRepository)
    {
        parent::__construct($employeeRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): ?Employee
    {
        $this->validateRequest($request, SearchEmployeeByIdRequest::class);

        /** @var SearchEmployeeByIdRequest $request */
        return $this->employeeRepository->find($request->employeeId());
    }
}
