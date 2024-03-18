<?php

namespace Core\Employee\Domain\Contracts;

use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;

interface EmployeeRepositoryContract
{
    public function find(EmployeeId $id): null|Employee;

    public function findCriteria(EmployeeIdentification $identification): null|Employee;

    public function save(Employee $employee): void;

    public function update(EmployeeId $id, Employee $employee): void;

    public function delete(EmployeeId $id): void;
    
    public function persistEmployee(Employee $employee): Employee;
}