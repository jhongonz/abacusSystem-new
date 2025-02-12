<?php

namespace Core\Employee\Infrastructure\Persistence\Repositories;

use Core\Employee\Domain\Contracts\EmployeeRepositoryContract;
use Core\Employee\Domain\Employee;
use Core\Employee\Domain\Employees;
use Core\Employee\Domain\ValueObjects\EmployeeId;
use Core\Employee\Domain\ValueObjects\EmployeeIdentification;
use Core\Employee\Exceptions\EmployeeNotFoundException;
use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee as EmployeeModel;
use Core\Employee\Infrastructure\Persistence\Translators\EmployeeTranslator;
use Core\SharedContext\Infrastructure\Persistence\ChainPriority;
use Core\SharedContext\Model\ValueObjectStatus;
use Illuminate\Database\DatabaseManager;

class EloquentEmployeeRepository implements ChainPriority, EmployeeRepositoryContract
{
    private const PRIORITY_DEFAULT = 50;

    public function __construct(
        private readonly DatabaseManager $database,
        private readonly EmployeeTranslator $employeeTranslator,
        private readonly EmployeeModel $model,
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
        $builder = $this->database->table($this->getTable())
            ->where('emp_id', $id->value())
            ->where('emp_state', '>', ValueObjectStatus::STATE_DELETE);

        $data = $builder->first();

        if (is_null($data)) {
            throw new EmployeeNotFoundException('Employee not found with id: '.$id->value());
        }

        $employeeModel = $this->updateAttributesModel((array) $data);

        return $this->employeeTranslator->setModel($employeeModel)->toDomain();
    }

    /**
     * @throws EmployeeNotFoundException
     * @throws \Exception
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

        $employeeModel = $this->updateAttributesModel((array) $data);

        return $this->employeeTranslator->setModel($employeeModel)->toDomain();
    }

    /**
     * @throws EmployeeNotFoundException
     * @throws \Exception
     */
    public function delete(EmployeeId $id): void
    {
        $builder = $this->database->table($this->getTable());
        $builder->where('emp_id', $id->value());
        $data = $builder->first();

        if (is_null($data)) {
            throw new EmployeeNotFoundException('Employee not found with id: '.$id->value());
        }

        $employeeModel = $this->updateAttributesModel((array) $data);
        $employeeModel->changeState(ValueObjectStatus::STATE_DELETE);
        $employeeModel->changeDeletedAt($this->getDateTime());

        $builder->update($employeeModel->toArray());
    }

    /**
     * @throws \Exception
     */
    public function persistEmployee(Employee $employee): Employee
    {
        $employeeModel = $this->domainToModel($employee);
        $employeeId = $employeeModel->id();
        $dataModel = $employeeModel->toArray();

        $builder = $this->database->table($this->getTable());

        if (is_null($employeeId)) {
            $dataModel['created_at'] = $this->getDateTime();

            $employeeId = $builder->insertGetId($dataModel);
            $employee->id()->setValue($employeeId);
            $employee->createdAt()->setValue($dataModel['created_at']);
        } else {
            $dataModel['updated_at'] = $this->getDateTime();
            $employee->updatedAt()->setValue($dataModel['updated_at']);

            $builder->where('emp_id', $employeeId);
            $builder->update($dataModel);
        }

        return $employee;
    }

    /**
     * @param array{q?: string|null} $filters
     */
    public function getAll(array $filters = []): ?Employees
    {
        $builder = $this->database->table($this->getTable())
            ->where('emp_state', '>', ValueObjectStatus::STATE_DELETE);

        if (!empty($filters['q'])) {
            $builder->whereFullText($this->model->getSearchField(), $filters['q']);
        }

        $employeeCollection = $builder->get(['emp_id']);

        $collection = [];
        foreach ($employeeCollection as $item) {
            $employeeModel = $this->updateAttributesModel((array) $item);

            if (!is_null($employeeModel->id())) {
                $collection[] = $employeeModel->id();
            }
        }

        $employees = $this->employeeTranslator->setCollection($collection)->toDomainCollection();
        $employees->setFilters($filters);

        return $employees;
    }

    private function domainToModel(Employee $domain): EmployeeModel
    {
        $builder = $this->database->table($this->getTable());
        $builder->where('emp_id', $domain->id()->value());
        $data = $builder->first();

        $model = $this->updateAttributesModel((array) $data);

        $model->changeId($domain->id()->value());
        $model->changeIdentification($domain->identification()->value() ?? '');
        $model->changeIdentificationType($domain->identificationType()->value() ?? '');
        $model->changeName($domain->name()->value());
        $model->changeLastname($domain->lastname()->value() ?? '');
        $model->changePhone($domain->phone()->value() ?? '');
        $model->changeBirthdate($domain->birthdate()->value());
        $model->changeEmail($domain->email()->value() ?? '');
        $model->changeAddress($domain->address()->value());
        $model->changeObservations($domain->observations()->value());
        $model->changeState($domain->state()->value());
        $model->changeSearch($domain->search()->value() ?? '');
        $model->changeCreatedAt($domain->createdAt()->value());

        $image = $domain->image()->value();
        if (!is_null($image)) {
            $model->changeImage($image);
        }

        if (!is_null($domain->institutionId()->value())) {
            $model->changeInstitutionId($domain->institutionId()->value());
        }

        if (!is_null($domain->updatedAt()->value())) {
            $model->changeUpdatedAt($domain->updatedAt()->value());
        }

        return $model;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function updateAttributesModel(array $data = []): EmployeeModel
    {
        $this->model->fill($data);

        return $this->model;
    }

    private function getTable(): string
    {
        return $this->model->getTable();
    }

    private function getDateTime(): \DateTime
    {
        return new \DateTime('now');
    }
}
