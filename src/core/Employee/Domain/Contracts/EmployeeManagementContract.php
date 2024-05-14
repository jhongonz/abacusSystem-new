<?php

namespace Core\Employee\Domain\Contracts;

use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;

interface EmployeeManagementContract
{
    public function searchEmployeeById(EmployeeId $id): ?Employee;

    public function searchEmployeeByIdentification(EmployeeIdentification $identification): ?Employee;

    public function searchEmployees(array $filters = []): Employees;

    public function updateEmployee(EmployeeId $id, array $data): void;

    public function createEmployee(Employee $employee): void;

    public function deleteEmployee(EmployeeId $id): void;
}
