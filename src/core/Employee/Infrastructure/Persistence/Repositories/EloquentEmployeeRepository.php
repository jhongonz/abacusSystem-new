<?php

namespace Core\Employee\Infrastructure\Persistence\Repositories;

use App\Models\Employee as EmployeeModel;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Core\Employee\Infrastructure\Persistence\Translators\EmployeeTranslator;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\User\Infrastructure\Persistence\Translators\UserTranslator;
use Exception;

class EloquentEmployeeRepository implements EmployeeRepositoryContract, ChainPriority
{
    private const PRIORITY_DEFAULT = 50;

    private EmployeeModel $employeeModel;
    private EmployeeTranslator $employeeTranslator;
    private UserTranslator $userTranslator;
    private int $priority;
    
    public function __construct(
        EmployeeModel $model,
        EmployeeTranslator $translator,
        UserTranslator $userTranslator,
        int $priority = self::PRIORITY_DEFAULT
    ) {
        $this->employeeModel = $model;
        $this->employeeTranslator = $translator;
        $this->userTranslator = $userTranslator;
        $this->priority = $priority;
    }

    public function priority(): int
    {
        return $this->priority;
    }

    public function changePriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @throws EmployeeNotFoundException
     * @throws Exception
     */
    public function find(EmployeeId $id): null|Employee
    {
        try {
            /** @var EmployeeModel $employeeModel */
            $employeeModel = $this->employeeModel
                ->where('emp_id', $id->value())
                ->where('emp_state','>',EmployeeState::STATE_DELETE)
                ->firstOrFail();
        } catch (Exception $exception) {
            throw new EmployeeNotFoundException('Employee not found with id: '. $id->value());
        }

        return $this->employeeTranslator->setModel($employeeModel)->toDomain();
    }

    /**
     * @throws EmployeeNotFoundException
     * @throws Exception
     */
    public function findCriteria(EmployeeIdentification $identification): null|Employee
    {
        try {
            /** @var EmployeeModel $employeeModel */
            $employeeModel = $this->employeeModel
                ->where('emp_identification', $identification->value())
                ->where('emp_state','>',EmployeeState::STATE_DELETE)
                ->firstOrFail();
        } catch (Exception $exception) {
            throw new EmployeeNotFoundException('Employee not found with id: '. $identification->value());
        }

        $user = $this->userTranslator->setModel($employeeModel->user())->toDomain();
        $employee = $this->employeeTranslator->setModel($employeeModel)->toDomain();
        $employee->setUser($user);
        
        return $employee;
    }

    public function save(Employee $employee): void
    {
        // TODO: Implement save() method.
    }

    public function update(EmployeeId $id, Employee $employee): void
    {
        // TODO: Implement update() method.
    }

    public function delete(EmployeeId $id): void
    {
        // TODO: Implement delete() method.
    }

    public function persistEmployee(Employee $employee): Employee
    {
        return $employee;
    }
}