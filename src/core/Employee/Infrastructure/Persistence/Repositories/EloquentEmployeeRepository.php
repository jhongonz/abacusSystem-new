<?php

namespace Core\Employee\Infrastructure\Persistence\Repositories;

use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee as EmployeeModel;
use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Exceptions\EmployeeDeleteException;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Core\Employee\Exceptions\EmployeesNotFoundException;
use Core\Employee\Infrastructure\Persistence\Translators\EmployeeTranslator;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\SharedContext\Model\ValueObjectStatus;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class EloquentEmployeeRepository implements EmployeeRepositoryContract, ChainPriority
{
    private const PRIORITY_DEFAULT = 50;

    private EmployeeModel $model;
    private EmployeeTranslator $employeeTranslator;
    private DatabaseManager $database;
    private int $priority;

    public function __construct(
        DatabaseManager $database,
        EmployeeTranslator $translator,
        int $priority = self::PRIORITY_DEFAULT
    ) {
        $this->database = $database;
        $this->employeeTranslator = $translator;
        $this->priority = $priority;

        $this->model = $this->createModel();
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
        $data = $this->database->table($this->model->getTable())
            ->where('emp_id', $id->value())
            ->where('emp_state','>', ValueObjectStatus::STATE_DELETE)
            ->first();

        if (is_null($data)) {
            throw new EmployeeNotFoundException('Employee not found with id: '. $id->value());
        }

        $employeeModel =$this->createModel((array) $data);
        return $this->employeeTranslator->setModel($employeeModel)->toDomain();
    }

    /**
     * @throws EmployeeNotFoundException
     * @throws Exception
     */
    public function findCriteria(EmployeeIdentification $identification): null|Employee
    {
        $data = $this->database->table($this->model->getTable())
            ->where('emp_identification',$identification->value())
            ->where('emp_state','>',ValueObjectStatus::STATE_DELETE)
            ->first();

        if (is_null($data)) {
            throw new EmployeeNotFoundException('Employee not found with id: '. $identification->value());
        }

        $employeeModel = $this->createModel((array) $data);
        return $this->employeeTranslator->setModel($employeeModel)->toDomain();
    }

    /**
     * @throws EmployeeNotFoundException
     * @throws EmployeeDeleteException
     */
    public function delete(EmployeeId $id): void
    {
        $data = $this->database->table($this->model->getTable())->find($id->value());

        if (is_null($data)) {
            throw new EmployeeNotFoundException('Employee not found with id: '. $id->value());
        }
        $employeeModel = $this->createModel($data);

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
        $employeeModel = $this->domainToModel($employee);
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
            $queryBuilder = $this->database->table($this->model->getTable())
                    ->where('emp_state','>',ValueObjectStatus::STATE_DELETE);

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

    protected function domainToModel(Employee $domain, ?EmployeeModel $model = null): EmployeeModel
    {
        if (is_null($model)) {
            $data = $this->database->table($this->model->getTable())->find($domain->id()->value());
            $model = $this->createModel($data);
        }

        $model->changeId($domain->id()->value());
        $model->changeIdentification($domain->identification()->value());
        $model->changeIdentificationType($domain->identificationType()->value());
        $model->changeName($domain->name()->value());
        $model->changeLastname($domain->lastname()->value());
        $model->changePhone($domain->phone()->value());
        $model->changeBirthdate($domain->birthdate()->value());
        $model->changeEmail($domain->email()->value());
        $model->changeAddress($domain->address()->value());
        $model->changeObservations($domain->observations()->value());
        $model->changeImage($domain->image()->value());
        $model->changeState($domain->state()->value());
        $model->changeSearch($domain->search()->value());
        $model->changeCreatedAt($domain->createdAt()->value());

        if (!is_null($domain->updatedAt()->value())) {
            $model->changeUpdatedAt($domain->updatedAt()->value());
        }

        return $model;
    }

    protected function createModel(array $data = []): EmployeeModel
    {
        return new EmployeeModel($data);
    }
}
