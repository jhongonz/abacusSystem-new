<?php

namespace Core\Employee\Infrastructure\Persistence\Translators;

use App\Models\Employee as EmployeeModel;
use Core\Employee\Domain\Employee;

class DomainToModelEmployeeTranslator implements TranslatorContract
{
    private EmployeeModel $model;
    private string $canTranslate;

    public function __construct(EmployeeModel $model)
    {
        $this->model = $model;
        $this->canTranslate = Employee::class;
    }

    /**
     * @param Employee $domain
     * @param EmployeeModel $model
     * @return EmployeeModel
     */
    public function executeTranslate($domain,$model): EmployeeModel
    {
        if (is_null($model)) {
            $model = $this->model->where('emp_id', $domain->id()->value())->first() ?: $this->createModel();
        }

        $model->changeId($domain->id()->value());
        $model->changeIdentification($domain->identification()->value());
        $model->changeName($domain->name()->value());
        $model->changeLastname($domain->lastname()->value());
        $model->changePhone($domain->phone()->value());
        $model->changeBirthdate($domain->birthdate()->value());
        $model->changeEmail($domain->email()->value());
        $model->changeAddress($domain->address()->value());
        $model->changeState($domain->state()->value());
        $model->changeSearch($domain->search()->value());
        $model->changeCreatedAt($domain->createdAt()->value());

        if (!is_null($domain->updatedAt()->value())) {
            $model->changeUpdatedAt($domain->updatedAt()->value());
        }

        return $model;
    }

    public function canTranslate(): string
    {
        return $this->canTranslate;
    }

    public function canTranslateTo(): string
    {
        return EmployeeModel::class;
    }

    protected function createModel(): EmployeeModel
    {
        return new EmployeeModel();
    }
}
