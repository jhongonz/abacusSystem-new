<?php

namespace Core\Employee\Domain\Contracts;

use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;

interface EmployeeManagementContract
{
    public function searchEmployeeById(?int $id): ?Employee;

    public function searchEmployeeByIdentification(string $identification): ?Employee;

    public function searchEmployees(array $filters = []): Employees;

    public function updateEmployee(int $id, array $data): void;

    public function createEmployee(array $data): Employee;

    public function deleteEmployee(int $id): void;
}
