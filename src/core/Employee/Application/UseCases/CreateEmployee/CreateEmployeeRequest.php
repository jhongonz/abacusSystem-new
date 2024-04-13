<?php

namespace Core\Employee\Application\UseCases\CreateEmployee;

use Core\Employee\Application\UseCases\RequestService;
use Core\Employee\Domain\Employee;

class CreateEmployeeRequest implements RequestService
{
    private Employee $employee;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    public function employee(): Employee
    {
        return $this->employee;
    }
}
