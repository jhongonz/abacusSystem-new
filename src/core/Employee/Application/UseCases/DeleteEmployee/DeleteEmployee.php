<?php

namespace Core\Employee\Application\UseCases\DeleteEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Application\UseCases\UseCasesService;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;

class DeleteEmployee extends UseCasesService
{
    public function __construct(EmployeeRepositoryContract $employeeRepository)
    {
        parent::__construct($employeeRepository);
    }

    /**
     * @throws \Exception
     */
    public function execute(RequestService $request): null
    {
        $this->validateRequest($request, DeleteEmployeeRequest::class);

        /** @var DeleteEmployeeRequest $request */
        $this->employeeRepository->delete($request->employeeId());

        return null;
    }
}
