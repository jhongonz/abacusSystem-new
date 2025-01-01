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

    /**
     * @return array<string, mixed>
     */
    public function read(): array
    {
        return [
            Employee::TYPE => $this->retrieveData(),
        ];
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Exception
     */
    public function readToShare(): array
    {
        $data = $this->retrieveData();
        $data['state_literal'] = $this->employee->state()->formatHtmlToState();

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function retrieveData(): array
    {
        $data = [
            'id' => $this->employee->id()->value(),
            'userId' => $this->employee->userId()->value(),
            'institutionId' => $this->employee->institutionId()->value(),
            'identification' => $this->employee->identification()->value(),
            'identification_type' => $this->employee->identificationType()->value(),
            'name' => $this->employee->name()->value(),
            'lastname' => $this->employee->lastname()->value(),
            'phone' => $this->employee->phone()->value(),
            'email' => $this->employee->email()->value(),
            'address' => $this->employee->address()->value(),
            'observations' => $this->employee->observations()->value(),
            'image' => $this->employee->image()->value(),
            'search' => $this->employee->search()->value(),
            'state' => $this->employee->state()->value(),
            'createdAt' => $this->employee->createdAt()->toFormattedString(),
        ];

        $birthdate = $this->employee->birthdate()->toFormattedString();
        $data['birthdate'] = !empty($birthdate) ? $birthdate : null;

        $updatedAt = $this->employee->updatedAt()->toFormattedString();
        $data['updatedAt'] = !empty($updatedAt) ? $updatedAt : null;

        return $data;
    }
}
