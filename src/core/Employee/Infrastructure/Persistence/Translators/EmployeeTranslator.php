<?php

namespace Core\Employee\Infrastructure\Persistence\Translators;

use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee as EmployeeModel;
use Core\SharedContext\Infrastructure\Translators\TranslatorDomainContract;
use Exception;

class EmployeeTranslator implements TranslatorDomainContract
{
    private EmployeeFactoryContract $employeeFactory;

    private EmployeeModel $employee;

    private array $collection;

    public function __construct(
        EmployeeFactoryContract $employeeFactory,
    ) {
        $this->employeeFactory = $employeeFactory;
        $this->collection = [];
    }

    /**
     * @param  EmployeeModel  $model
     * @return $this
     */
    public function setModel($model): self
    {
        $this->employee = $model;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function toDomain(): Employee
    {
        $employee = $this->employeeFactory->buildEmployee(
            $this->employeeFactory->buildEmployeeId($this->employee->id()),
            $this->employeeFactory->buildEmployeeIdentification($this->employee->identification()),
            $this->employeeFactory->buildEmployeeName($this->employee->name()),
            $this->employeeFactory->buildEmployeeLastname($this->employee->lastname()),
            $this->employeeFactory->buildEmployeeState($this->employee->state()),
            $this->employeeFactory->buildEmployeeCreatedAt($this->employee->createdAt())
        );

        $employee->setIdentificationType($this->employeeFactory->buildEmployeeIdentificationType($this->employee->identificationType()));
        $employee->setUpdatedAt($this->employeeFactory->buildEmployeeUpdatedAt($this->employee->updatedAt()));

        $employee->setAddress($this->employeeFactory->buildEmployeeAddress($this->employee->address()));
        $employee->setPhone($this->employeeFactory->buildEmployeePhone($this->employee->phone()));
        $employee->setEmail($this->employeeFactory->buildEmployeeEmail($this->employee->email()));
        $employee->setUserId($this->employeeFactory->buildEmployeeUserId($this->employee->user()->id()));
        $employee->setSearch($this->employeeFactory->buildEmployeeSearch($this->employee->search()));
        $employee->setBirthdate($this->employeeFactory->buildEmployeeBirthdate($this->employee->birthdate()));
        $employee->setObservations($this->employeeFactory->buildEmployeeObservations($this->employee->observations()));
        $employee->setImage($this->employeeFactory->buildEmployeeImage($this->employee->image()));

        return $employee;
    }

    public function setCollection(array $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    public function toDomainCollection(): Employees
    {
        $employees = new Employees;
        foreach ($this->collection as $id) {
            $employees->addId($id);
        }

        return $employees;
    }
}
