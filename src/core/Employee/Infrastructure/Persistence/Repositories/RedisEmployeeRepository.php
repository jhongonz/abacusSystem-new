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
use Exception;
use Illuminate\Support\Facades\Redis;

class RedisEmployeeRepository implements EmployeeRepositoryContract, ChainPriority
{
    /**@var int*/
    private const PRIORITY_DEFAULT = 100;

    /** @var string */
    private const EMPLOYEE_KEY_FORMAT = '%s::%s';

    private int $priority;
    private string $keyPrefix;

    private EmployeeFactoryContract $employeeFactory;
    private EmployeeDataTransformerContract $dataTransformer;

    public function __construct(
      EmployeeFactoryContract $employeeFactory,
      EmployeeDataTransformerContract $dataTransformer,
      string $keyPrefix = 'employee',
      int $priority = self::PRIORITY_DEFAULT
    ) {
        $this->employeeFactory = $employeeFactory;
        $this->dataTransformer = $dataTransformer;
        $this->keyPrefix = $keyPrefix;
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
     */
    public function find(EmployeeId $id): null|Employee
    {
        try {
            $data = Redis::get($this->employeeKey($id));
        } catch (Exception $exception) {
            throw new EmployeeNotFoundException('Employee not found by id '. $id->value());
        }

        if (!is_null($data)) {
            $dataArray = json_decode($data, true);

            /** @var Employee */
            return $this->employeeFactory->buildEmployeeFromArray($dataArray);
        }

        return null;
    }

    /**
     * @throws EmployeeNotFoundException
     */
    public function findCriteria(EmployeeIdentification $identification): null|Employee
    {
        try {
            $data = Redis::get($this->employeeIdentificationKey($identification));
        } catch (Exception $exception) {
            throw new EmployeeNotFoundException('Employee not found by identification '. $identification->value());
        }

        if (!is_null($data)) {
            $dataArray = json_decode($data, true);

            /** @var Employee */
            return $this->employeeFactory->buildEmployeeFromArray($dataArray);
        }

        return null;
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
        } catch (Exception $exception) {
            throw new EmployeePersistException('It could not persist Employee with key '.$employeeKey.' in redis');
        }

        return $employee;
    }

    public function persistEmployees(Employees $employees): Employees
    {
        return $employees;
    }

    private function employeeKey(EmployeeId $id): string
    {
        return sprintf(self::EMPLOYEE_KEY_FORMAT, $this->keyPrefix, $id->value());
    }

    private function employeeIdentificationKey(EmployeeIdentification $identification): string
    {
        return sprintf(self::EMPLOYEE_KEY_FORMAT, $this->keyPrefix, $identification->value());
    }

    public function getAll(array $filters = []): null|Employees
    {
        return null;
    }
}
