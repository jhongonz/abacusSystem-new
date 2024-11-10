<?php

namespace Core\Employee\Domain\Contracts;

use Core\Employee\Domain\Employee;

interface EmployeeDataTransformerContract
{
    public function write(Employee $employee): EmployeeDataTransformerContract;

    /**
     * @return array<string, mixed>
     */
    public function read(): array;

    /**
     * @return array<string, mixed>
     */
    public function readToShare(): array;
}
