<?php

namespace Core\Employee\Domain\Contracts;

use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;

interface EmployeeRepositoryContract
{
    public function find(EmployeeId $id): ?Employee;

    public function findCriteria(EmployeeIdentification $identification): ?Employee;

    /**
     * @param array<string, mixed> $filters
     */
    public function getAll(array $filters = []): ?Employees;

    public function delete(EmployeeId $id): void;

    public function persistEmployee(Employee $employee): Employee;
}
