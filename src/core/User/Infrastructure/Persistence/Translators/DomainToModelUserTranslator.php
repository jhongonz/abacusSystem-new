<?php

namespace Core\User\Infrastructure\Persistence\Translators;

use App\Models\User as UserModel;
use Core\User\Domain\User;

class DomainToModelUserTranslator implements TranslatorContract
{
    private UserModel $model;
    private string $canTranslate;

    public function __construct(UserModel $model)
    {
        $this->model = $model;
        $this->canTranslate = User::class;
    }

    /**
     * @param User $domain
     * @param UserModel $model
     * @return UserModel
     */
    public function executeTranslate($domain,$model = null): UserModel
    {
        if (is_null($model)) {
            $model = $this->model->where('user_login', $domain->login()->value())->first() ?: $this->createModel();
        }

        $model->changeId($domain->id()->value());
        $model->changeEmployeeId($domain->employeeId()->value());
        $model->changeProfileId($domain->profileId()->value());
        $model->changeLogin($domain->login()->value());
        $model->changePassword($domain->password()->value());
        $model->changeState($domain->state()->value());
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
        return UserModel::class;
    }

    protected function createModel(): UserModel
    {
        return new UserModel();
    }
}
