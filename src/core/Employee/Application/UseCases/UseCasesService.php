<?php

namespace Core\Employee\Application\UseCases;

use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Exception;

abstract class UseCasesService implements ServiceContract
{
    public function __construct(
        protected readonly EmployeeRepositoryContract $employeeRepository
    ) {
    }

    /**
     * @throws Exception
     */
    protected function validateRequest(RequestService $request, string $requestClass): RequestService
    {
        if (! $request instanceof $requestClass) {
            throw new Exception('Request not valid');
        }

        return $request;
    }
}
