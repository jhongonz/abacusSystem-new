<?php

namespace Core\Employee\Infrastructure\Persistence\Repositories;

use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Exception;
use Throwable;

class ChainEmployeeRepository extends AbstractChainRepository implements EmployeeRepositoryContract
{
    private const FUNCTION_NAMES = [
        Employee::class => 'persistEmployee'
    ];

    private string $domainToPersist;
    
    function functionNamePersist(): string
    {
        return self::FUNCTION_NAMES[$this->domainToPersist];
    }

    /**
     * @throws Throwable
     * @throws EmployeeNotFoundException
     */
    public function find(EmployeeId $id): null|Employee
    {
        $this->domainToPersist = Employee::class;

        try {
            return $this->read(__FUNCTION__, $id);
        } catch (Exception $exception) {
            throw new EmployeeNotFoundException('Employee not found by id '. $id->value());
        }
    }

    /**
     * @throws Throwable
     * @throws EmployeeNotFoundException
     */
    public function findCriteria(EmployeeIdentification $identification): null|Employee
    {
        $this->domainToPersist = Employee::class;

        try {
            return $this->read(__FUNCTION__, $identification);
        } catch (Exception $exception) {
            throw new EmployeeNotFoundException('Employee not found by identification '. $identification->value());
        }
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
        return $this->write(__FUNCTION__, $employee);
    }
}