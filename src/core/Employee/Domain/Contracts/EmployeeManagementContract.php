<?php

namespace Core\Employee\Domain\Contracts;

use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;

interface EmployeeManagementContract
{
    public function searchEmployeeById(?int $id): ?Employee;

    public function searchEmployeeByIdentification(string $identification): ?Employee;

    /**
     * @param array<string, mixed> $filters
     */
    public function searchEmployees(array $filters = []): Employees;

    /**
     * @param array<string, mixed> $data
     */
    public function updateEmployee(int $id, array $data): Employee;

    /**
     * @param array<string, mixed> $data
     */
    public function createEmployee(array $data): Employee;

    public function deleteEmployee(int $id): void;
}
