<?php

namespace Core\Employee\Application\DataTransformer;

use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Employee;
use Exception;

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
            Employee::TYPE => $this->retrieveData(),
        ];
    }

    /**
     * @throws Exception
     */
    public function readToShare(): array
    {
        $data = $this->retrieveData();
        $data['state_literal'] = $this->employee->state()->formatHtmlToState();

        return $data;
    }

    private function retrieveData(): array
    {
        return [
            'id' => $this->employee->id()->value(),
            'userId' => $this->employee->userId()->value(),
            'identification' => $this->employee->identification()->value(),
            'identification_type' => $this->employee->identificationType()->value(),
            'name' => $this->employee->name()->value(),
            'lastname' => $this->employee->lastname()->value(),
            'phone' => $this->employee->phone()->value(),
            'email' => $this->employee->email()->value(),
            'address' => $this->employee->address()->value(),
            'birthdate' => $this->employee->birthdate()->value(),
            'observations' => $this->employee->observations()->value(),
            'image' => $this->employee->image()->value(),
            'search' => $this->employee->search()->value(),
            'state' => $this->employee->state()->value(),
            'createdAt' => $this->employee->createdAt()->value(),
            'updatedAt' => $this->employee->updatedAt()->value(),
        ];
    }
}
