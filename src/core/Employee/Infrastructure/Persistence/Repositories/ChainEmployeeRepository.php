<?php

namespace Core\Employee\Infrastructure\Persistence\Repositories;

use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Core\Employee\Exceptions\EmployeesNotFoundException;
use Core\SharedContext\Infrastructure\Persistence\AbstractChainRepository;
use Exception;
use Throwable;

class ChainEmployeeRepository extends AbstractChainRepository implements EmployeeRepositoryContract
{
    private const FUNCTION_NAMES = [
        Employee::class => 'persistEmployee',
        Employees::class => 'persistEmployees'
    ];

    private string $domainToPersist;
    private bool $deleteSource = false;

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

    public function delete(EmployeeId $id): void
    {
        $this->deleteSource = true;
    }

    public function persistEmployee(Employee $employee): Employee
    {
        return $this->write(__FUNCTION__, $employee);
    }

    public function persistEmployees(Employees $employees): Employees
    {
        return $this->write(__FUNCTION__, $employees);
    }

    /**
     * @throws Throwable
     * @throws EmployeeNotFoundException
     */
    public function getAll(array $filters = []): null|Employees
    {
        $this->domainToPersist = Employees::class;

        try {
            return $this->read(__FUNCTION__, $filters);
        } catch (Exception $exception) {
            throw new EmployeesNotFoundException('Employees no found');
        }
    }

    function functionNameDelete(): bool
    {
        return $this->deleteSource;
    }
}
