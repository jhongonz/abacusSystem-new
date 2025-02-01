<?php

namespace Core\Employee\Infrastructure\Persistence\Repositories;

use Core\Employee\Domain\Contracts\EmployeeDataTransformerContract;
use Core\Employee\Domain\Contracts\EmployeeFactoryContract;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Core\Employee\Exceptions\EmployeePersistException;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Illuminate\Support\Facades\Redis;
use Psr\Log\LoggerInterface;

class RedisEmployeeRepository implements ChainPriority, EmployeeRepositoryContract
{
    /** @var int */
    private const PRIORITY_DEFAULT = 100;

    /** @var string */
    private const EMPLOYEE_KEY_FORMAT = '%s::%s';

    public function __construct(
        private readonly EmployeeFactoryContract $employeeFactory,
        private readonly EmployeeDataTransformerContract $dataTransformer,
        private readonly LoggerInterface $logger,
        private readonly string $keyPrefix = 'employee',
        private int $priority = self::PRIORITY_DEFAULT,
    ) {
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
     * @throws \Exception
     */
    public function find(EmployeeId $id): ?Employee
    {
        try {
            /** @var string $data */
            $data = Redis::get($this->employeeKey($id));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new EmployeeNotFoundException('Employee not found by id '.$id->value());
        }

        if (!empty($data)) {
            /** @var array<string, mixed> $dataArray */
            $dataArray = json_decode($data, true);

            /* @var Employee */
            return $this->employeeFactory->buildEmployeeFromArray($dataArray);
        }

        return null;
    }

    /**
     * @throws EmployeeNotFoundException
     */
    public function findCriteria(EmployeeIdentification $identification): ?Employee
    {
        try {
            /** @var string $data */
            $data = Redis::get($this->employeeIdentificationKey($identification));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new EmployeeNotFoundException('Employee not found by identification '.$identification->value());
        }

        if (!empty($data)) {
            /** @var array<string, mixed> $dataArray */
            $dataArray = json_decode($data, true);

            /* @var Employee */
            return $this->employeeFactory->buildEmployeeFromArray($dataArray);
        }

        return null;
    }

    public function delete(EmployeeId $id): void
    {
        Redis::delete($this->employeeKey($id));
    }

    /**
     * @throws EmployeePersistException
     */
    public function persistEmployee(Employee $employee): Employee
    {
        $employeeKey = $this->employeeKey($employee->id());
        $employeeIdentificationKey = $this->employeeIdentificationKey($employee->identification());

        $employeeData = $this->dataTransformer->write($employee)->read();
        try {
            Redis::set($employeeKey, json_encode($employeeData));
            Redis::set($employeeIdentificationKey, json_encode($employeeData));
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());
            throw new EmployeePersistException('It could not persist Employee with key '.$employeeKey.' in redis');
        }

        return $employee;
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function getAll(array $filters = []): ?Employees
    {
        return null;
    }

    private function employeeKey(EmployeeId $id): string
    {
        return sprintf(self::EMPLOYEE_KEY_FORMAT, $this->keyPrefix, $id->value());
    }

    private function employeeIdentificationKey(EmployeeIdentification $identification): string
    {
        return sprintf(self::EMPLOYEE_KEY_FORMAT, $this->keyPrefix, $identification->value());
    }
}
