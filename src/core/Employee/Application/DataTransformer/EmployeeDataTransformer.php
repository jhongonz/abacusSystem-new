<?php

namespace Core\Employee\Application\DataTransformer;

use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Employee;

class EmployeeDataTransformer implements EmployeeDataTransformerContract
{
    private Employee $employee;
    
    public function write(Employee $employee): self
    {
        $this->employee = $employee;
        return $this;
    }

    public function read(): array
    {
        return [
            Employee::TYPE => [
                'id' => $this->employee->id()->value(),
                'identification' => $this->employee->identification()->value(),
                'name' => $this->employee->name()->value(),
                'lastname' => $this->employee->lastname()->value(),
                'phone' => $this->employee->phone()->value(),
                'email' => $this->employee->email()->value(),
                'address' => $this->employee->address()->value(),
                'state' => $this->employee->state()->value(),
                'createdAt' => $this->employee->createdAt()->value(),
                'updatedAt' => $this->employee->updatedAt()->value(),
            ]
        ];
    }
}