<?php

namespace Core\Employee\Application\UseCases;

use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Exception;

abstract class UseCasesService implements ServiceContract
{
    protected EmployeeRepositoryContract $employeeRepository;
    
    public function __construct(
        EmployeeRepositoryContract $employeeRepository
    ) {
        $this->employeeRepository = $employeeRepository;
    }
    
    /**
     * @throws Exception
     */
    protected function validateRequest(RequestService $request, string $requestClass): RequestService
    {
        if (!$request instanceof $requestClass) {
            throw new Exception('Request not valid');
        }

        return $request; 
    }
}