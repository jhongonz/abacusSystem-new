<?php

namespace Core\Employee\Infrastructure\Persistence\Translators;

use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee as EmployeeModel;
use Core\User\Infrastructure\Persistence\Eloquent\Model\User;

class EmployeeTranslator
{
    private EmployeeModel $employee;

    /** @var array<int<0, max>, int> */
    private array $collection = [];

    public function __construct(
        private readonly EmployeeFactoryContract $employeeFactory,
    ) {
    }

    public function setModel(EmployeeModel $model): self
    {
        $this->employee = $model;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function toDomain(): Employee
    {
        $employee = $this->employeeFactory->buildEmployee(
            $this->employeeFactory->buildEmployeeId($this->employee->id()),
            $this->employeeFactory->buildEmployeeIdentification($this->employee->identification() ?? ''),
            $this->employeeFactory->buildEmployeeName($this->employee->name() ?? ''),
            $this->employeeFactory->buildEmployeeLastname($this->employee->lastname() ?? ''),
            $this->employeeFactory->buildEmployeeState($this->employee->state())
        );

        $employee->setIdentificationType($this->employeeFactory->buildEmployeeIdentificationType($this->employee->identificationType()));
        $employee->setUpdatedAt($this->employeeFactory->buildEmployeeUpdatedAt($this->employee->updatedAt()));

        if (!is_null($this->employee->createdAt())) {
            $employee->setCreatedAt($this->employeeFactory->buildEmployeeCreatedAt($this->employee->createdAt()));
        }

        $employee->setAddress($this->employeeFactory->buildEmployeeAddress($this->employee->address()));
        $employee->setPhone($this->employeeFactory->buildEmployeePhone($this->employee->phone()));
        $employee->setEmail($this->employeeFactory->buildEmployeeEmail($this->employee->email()));
        $employee->setSearch($this->employeeFactory->buildEmployeeSearch($this->employee->search()));
        $employee->setBirthdate($this->employeeFactory->buildEmployeeBirthdate($this->employee->birthdate()));
        $employee->setObservations($this->employeeFactory->buildEmployeeObservations($this->employee->observations()));
        $employee->setImage($this->employeeFactory->buildEmployeeImage($this->employee->image()));
        $employee->setInstitutionId($this->employeeFactory->buildEmployeeInstitutionId($this->employee->institutionId()));

        /** @var User|null $user */
        $user = $this->employee->relationWithUser()->first(['user_id']);

        $userId = $user ? $user->id() : null;
        $employee->setUserId($this->employeeFactory->buildEmployeeUserId($userId));

        return $employee;
    }

    /**
     * @param array<int<0, max>, int> $collection
     *
     * @return $this
     */
    public function setCollection(array $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    public function toDomainCollection(): Employees
    {
        $employees = new Employees();
        foreach ($this->collection as $id) {
            $employees->addId($id);
        }

        return $employees;
    }
}
