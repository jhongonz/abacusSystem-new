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

/**
 * @codeCoverageIgnore
 */
class ChainEmployeeRepository extends AbstractChainRepository implements EmployeeRepositoryContract
{
    private const FUNCTION_NAME = 'persistEmployee';

    public function functionNamePersist(): string
    {
        return self::FUNCTION_NAME;
    }

    /**
     * @throws Throwable
     * @throws EmployeeNotFoundException
     */
    public function find(EmployeeId $id): ?Employee
    {
        try {
            return $this->read(__FUNCTION__, $id);
        } catch (Exception $exception) {
            throw new EmployeeNotFoundException('Employee not found by id '.$id->value());
        }
    }

    /**
     * @throws Throwable
     * @throws EmployeeNotFoundException
     */
    public function findCriteria(EmployeeIdentification $identification): ?Employee
    {
        try {
            return $this->read(__FUNCTION__, $identification);
        } catch (Exception $exception) {
            throw new EmployeeNotFoundException('Employee not found by identification '.$identification->value());
        }
    }

    /**
     * @throws Throwable
     */
    public function delete(EmployeeId $id): void
    {
        $this->write(__FUNCTION__, $id);
    }

    /**
     * @throws Exception
     */
    public function persistEmployee(Employee $employee): Employee
    {
        return $this->write(__FUNCTION__, $employee);
    }

    /**
     * @throws Exception
     */
    public function persistEmployees(Employees $employees): Employees
    {
        return $this->write(__FUNCTION__, $employees);
    }

    /**
     * @throws Throwable
     * @throws EmployeeNotFoundException
     */
    public function getAll(array $filters = []): ?Employees
    {
        try {
            return $this->read(__FUNCTION__, $filters);
        } catch (Exception $exception) {
            throw new EmployeesNotFoundException('Employees not found');
        }
    }
}
