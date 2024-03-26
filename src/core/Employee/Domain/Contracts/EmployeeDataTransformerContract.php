<?php

namespace Core\Employee\Domain\Contracts;

use Core\Employee\Domain\Employee;

interface EmployeeDataTransformerContract
{
    public function write(Employee $employee): EmployeeDataTransformerContract;

    public function read(): array;

    public function readToShare(): array;
}
