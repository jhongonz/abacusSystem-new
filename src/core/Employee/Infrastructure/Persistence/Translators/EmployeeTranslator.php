<?php

namespace Core\Employee\Infrastructure\Persistence\Translators;

use App\Models\Employee as EmployeeModel;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Employee;
use Core\SharedContext\Infrastructure\Translators\TranslatorDomainContract;
use DateTime;
use Exception;

class EmployeeTranslator implements TranslatorDomainContract
{
    private EmployeeFactoryContract $employeeFactory;
    private EmployeeModel $employee;

    public function __construct(
        EmployeeFactoryContract $employeeFactory,
        EmployeeModel $employee,
    ) {
        $this->employeeFactory = $employeeFactory;
        $this->employee = $employee;
    }

    /**
     * @param EmployeeModel $model
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
            $this->employeeFactory->buildEmployeeCreatedAt(
                new DateTime($this->employee->createdAt())
            )
        );

        $employee->setUpdatedAt($this->employeeFactory->buildEmployeeUpdatedAt(
            new DateTime($this->employee->updatedAt())
        ));

        $employee->setAddress($this->employeeFactory->buildEmployeeAddress($this->employee->address()));
        $employee->setPhone($this->employeeFactory->buildEmployeePhone($this->employee->phone()));
        $employee->setEmail($this->employeeFactory->buildEmployeeEmail($this->employee->email()));
        $employee->setUserId($this->employeeFactory->buildEmployeeUserId($this->employee->user()->id()));

        return $employee;
    }

    public function canTranslate(): string
    {
        return EmployeeModel::class;
    }
}
