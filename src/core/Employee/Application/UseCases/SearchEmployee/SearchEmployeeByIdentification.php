<?php

namespace Core\Employee\Application\UseCases\SearchEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;

class SearchEmployeeByIdentification extends UseCasesService
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
        $this->validateRequest($request, SearchEmployeeByIdentificationRequest::class);

        /* @var SearchEmployeeByIdentificationRequest $request */
        return $this->employeeRepository->findCriteria($request->employeeIdentification());
    }
}
