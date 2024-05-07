<?php

namespace Core\Employee\Infrastructure\Persistence\Repositories;

use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Core\Employee\Exceptions\EmployeesNotFoundException;
use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee as EmployeeModel;
use Core\Employee\Infrastructure\Persistence\Translators\EmployeeTranslator;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\SharedContext\Model\ValueObjectStatus;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Builder;

class EloquentEmployeeRepository implements ChainPriority, EmployeeRepositoryContract
{
    private const PRIORITY_DEFAULT = 50;

    private EmployeeModel $model;

    private EmployeeTranslator $employeeTranslator;

    private DatabaseManager $database;

    private int $priority;

    public function __construct(
        DatabaseManager $database,
        EmployeeTranslator $translator,
        EmployeeModel $model,
        int $priority = self::PRIORITY_DEFAULT
    ) {
        $this->database = $database;
        $this->employeeTranslator = $translator;
        $this->priority = $priority;

        $this->model = $model;
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
    public function find(EmployeeId $id): ?Employee
    {
        $builder = $this->database->table($this->getTable())
            ->where('emp_id', $id->value())
            ->where('emp_state', '>', ValueObjectStatus::STATE_DELETE);

        $data = $builder->first();

        if (is_null($data)) {
            throw new EmployeeNotFoundException('Employee not found with id: '.$id->value());
        }

        $employeeModel = $this->updateAttributesModelEmployee($data->toArray());

        return $this->employeeTranslator->setModel($employeeModel)->toDomain();
    }

    /**
     * @throws EmployeeNotFoundException
     * @throws Exception
     */
    public function findCriteria(EmployeeIdentification $identification): ?Employee
    {
        $builder = $this->database->table($this->getTable())
            ->where('emp_identification', $identification->value())
            ->where('emp_state', '>', ValueObjectStatus::STATE_DELETE);
        $data = $builder->first();

        if (is_null($data)) {
            throw new EmployeeNotFoundException('Employee not found with id: '.$identification->value());
        }

        $employeeModel = $this->updateAttributesModelEmployee($data->toArray());

        return $this->employeeTranslator->setModel($employeeModel)->toDomain();
    }

    /**
     * @throws EmployeeNotFoundException
     */
    public function delete(EmployeeId $id): void
    {
        $builder = $this->database->table($this->getTable());
        $data = $builder->find($id->value());

        if (is_null($data)) {
            throw new EmployeeNotFoundException('Employee not found with id: '.$id->value());
        }

        $builder->where('emp_id', $id->value());
        $builder->delete();
    }

    /**
     * @throws Exception
     */
    public function persistEmployee(Employee $employee): Employee
    {
        $employeeModel = $this->domainToModel($employee);
        $employeeId = $employeeModel->id();
        $dataModel = $employeeModel->toArray();

        $builder = $this->database->table($this->getTable());

        if (is_null($employeeId)) {
            $employeeId = $builder->insertGetId($dataModel);
            $employee->id()->setValue($employeeId);
        } else {
            $builder->where('emp_id', $employeeId);
            $builder->update($dataModel);
        }

        return $employee;
    }

    /**
     * @throws EmployeesNotFoundException
     */
    public function getAll(array $filters = []): ?Employees
    {
        /** @var Builder $builder */
        $builder = $this->database->table($this->getTable())
            ->where('emp_state', '>', ValueObjectStatus::STATE_DELETE);

        if (array_key_exists('q', $filters) && isset($filters['q'])) {
            $builder->whereFullText($this->model->getSearchField(), $filters['q']);
        }
        $employeeCollection = $builder->get(['emp_id']);

        if (empty($employeeCollection)) {
            throw new EmployeesNotFoundException('Employees not found');
        }

        $collection = [];
        foreach ($employeeCollection as $item) {
            $employeeModel = $this->updateAttributesModelEmployee((array) $item);
            $collection[] = $employeeModel->id();
        }

        $employees = $this->employeeTranslator->setCollection($collection)->toDomainCollection();
        $employees->setFilters($filters);

        return $employees;
    }

    private function domainToModel(Employee $domain): EmployeeModel
    {
        $builder = $this->database->table($this->getTable());
        $data = (array) $builder->find($domain->id()->value());
        $model = $this->updateAttributesModelEmployee($data);

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

        if (! is_null($domain->updatedAt()->value())) {
            $model->changeUpdatedAt($domain->updatedAt()->value());
        }

        return $model;
    }

    private function updateAttributesModelEmployee(array $data = []): EmployeeModel
    {
        $this->model->fill($data);

        return $this->model;
    }

    private function getTable(): string
    {
        return $this->model->getTable();
    }
}
