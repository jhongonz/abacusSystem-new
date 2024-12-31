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

        $employee->identificationType()->setValue($this->employee->identificationType());

        if (!is_null($this->employee->createdAt())) {
            $employee->createdAt()->setValue($this->employee->createdAt());
        }

        if (!is_null($this->employee->updatedAt())) {
            $employee->updatedAt()->setValue($this->employee->updatedAt());
        }

        $employee->address()->setValue($this->employee->address());

        /** @var string $phone */
        $phone = $this->employee->phone();
        $employee->phone()->setValue($phone);

        /** @var string $email */
        $email = $this->employee->email();
        $employee->email()->setValue($email);

        $employee->search()->setValue($this->employee->search());
        $employee->birthdate()->setValue($this->employee->birthdate());
        $employee->observations()->setValue($this->employee->observations());

        /** @var string $image */
        $image = $this->employee->image();
        $employee->image()->setValue($image);

        /** @var int $institutionId */
        $institutionId = $this->employee->institutionId();
        $employee->institutionId()->setValue($institutionId);

        /** @var User|null $user */
        $user = $this->employee->relationWithUser()->first(['user_id']);

        /** @var int|null $userId */
        $userId = $user?->id();
        $employee->userId()->setValue($userId);

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
