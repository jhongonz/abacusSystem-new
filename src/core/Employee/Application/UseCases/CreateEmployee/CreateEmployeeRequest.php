<?php

namespace Core\Employee\Application\UseCases\CreateEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Domain\Employee;

class CreateEmployeeRequest implements RequestService
{
    public function __construct(
        private readonly Employee $employee
    ) {
    }

    public function employee(): Employee
    {
        return $this->employee;
    }
}
