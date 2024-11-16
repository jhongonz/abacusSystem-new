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

class ChainEmployeeRepository extends AbstractChainRepository implements EmployeeRepositoryContract
{
    private const FUNCTION_NAME = 'persistEmployee';

    public function functionNamePersist(): string
    {
        return self::FUNCTION_NAME;
    }

    /**
     * @throws \Throwable
     * @throws EmployeeNotFoundException
     */
    public function find(EmployeeId $id): ?Employee
    {
        try {
            /** @var Employee|null $result */
            $result = $this->read(__FUNCTION__, $id);

            return $result;
        } catch (\Exception $exception) {
            throw new EmployeeNotFoundException('Employee not found by id '.$id->value());
        }
    }

    /**
     * @throws \Throwable
     * @throws EmployeeNotFoundException
     */
    public function findCriteria(EmployeeIdentification $identification): ?Employee
    {
        try {
            /** @var Employee|null $result */
            $result = $this->read(__FUNCTION__, $identification);

            return $result;
        } catch (\Exception $exception) {
            throw new EmployeeNotFoundException('Employee not found by identification '.$identification->value());
        }
    }

    /**
     * @throws \Throwable
     */
    public function delete(EmployeeId $id): void
    {
        $this->write(__FUNCTION__, $id);
    }

    /**
     * @throws \Exception
     */
    public function persistEmployee(Employee $employee): Employee
    {
        /** @var Employee $result */
        $result = $this->write(__FUNCTION__, $employee);

        return $result;
    }

    /**
     * @param array<string, mixed> $filters
     *
     * @throws EmployeesNotFoundException
     * @throws \Throwable
     */
    public function getAll(array $filters = []): ?Employees
    {
        $this->canPersist = false;

        try {
            /** @var Employees|null $result */
            $result = $this->read(__FUNCTION__, $filters);

            return $result;
        } catch (\Exception $exception) {
            throw new EmployeesNotFoundException('Employees not found');
        }
    }
}
