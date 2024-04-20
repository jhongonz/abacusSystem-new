<?php

namespace Core\Employee\Infrastructure\Persistence\Repositories;

use App\Models\Employee as EmployeeModel;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Domain\ValueObjects\EmployeeState;
use Core\Employee\Exceptions\EmployeeDeleteException;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Core\Employee\Exceptions\EmployeesNotFoundException;
use Core\Employee\Infrastructure\Persistence\Translators\DomainToModelEmployeeTranslator;
use Core\Employee\Infrastructure\Persistence\Translators\EmployeeTranslator;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\NoReturn;
use Throwable;

class EloquentEmployeeRepository implements EmployeeRepositoryContract, ChainPriority
{
    private const PRIORITY_DEFAULT = 50;

    private EmployeeModel $employeeModel;
    private EmployeeTranslator $employeeTranslator;
    private DomainToModelEmployeeTranslator $modelEmployeeTranslator;
    private int $priority;

    public function __construct(
        EmployeeModel $model,
        EmployeeTranslator $translator,
        DomainToModelEmployeeTranslator $modelEmployeeTranslator,
        int $priority = self::PRIORITY_DEFAULT
    ) {
        $this->employeeModel = $model;
        $this->employeeTranslator = $translator;
        $this->modelEmployeeTranslator = $modelEmployeeTranslator;
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

        return $this->employeeTranslator->setModel($employeeModel)->toDomain();
    }

    /**
     * @throws EmployeeNotFoundException
     * @throws EmployeeDeleteException
     */
    #[NoReturn] public function delete(EmployeeId $id): void
    {
        /** @var EmployeeModel $employeeModel */
        $employeeModel = $this->employeeModel->where('emp_id', $id->value())
            ->where('emp_state','>',EmployeeState::STATE_DELETE)
            ->first();

        if (is_null($employeeModel)) {
            throw new EmployeeNotFoundException('Employee not found with id: '. $id->value());
        }

        try {
            $employeeModel->deleteOrFail();
        } catch (Throwable $exception) {
            throw new EmployeeDeleteException($exception->getMessage(), $exception->getTrace());
        }
    }

    /**
     * @throws Exception
     */
    public function persistEmployee(Employee $employee): Employee
    {
        $employeeModel = $this->modelEmployeeTranslator->executeTranslate($employee);
        $employeeModel->save();

        $employee->id()->setValue($employeeModel->id());

        return $employee;
    }

    /**
     * @throws EmployeesNotFoundException
     */
    public function getAll(array $filters = []): null|Employees
    {
        try {
            /**@var  Builder $queryBuilder*/
            $queryBuilder = $this->employeeModel->where('emp_state','>',EmployeeState::STATE_DELETE);

            if (array_key_exists('q', $filters) && isset($filters['q'])) {
                $queryBuilder->where('emp_search','like','%'.$filters['q'].'%');
            }

            $employeeCollection = $queryBuilder->get(['emp_id']);
        } catch (Exception $exception) {
            throw new EmployeesNotFoundException('Employees not found');
        }

        $collection = [];
        /**@var EmployeeModel $employeeModel*/
        foreach ($employeeCollection as $employeeModel) {
            $collection[] = $employeeModel->id();
        }

        $employees = $this->employeeTranslator->setCollection($collection)->toDomainCollection();
        $employees->setFilters($filters);

        return $employees;
    }
}
