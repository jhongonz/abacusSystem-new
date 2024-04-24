<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Infrastructure\Persistence\Translators;

use Core\User\Infrastructure\Persistence\Eloquent\Model\User as UserModel;
use Core\SharedContext\Infrastructure\Translators\TranslatorDomainContract;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\User;
use Exception;

class UserTranslator implements TranslatorDomainContract
{
    private UserFactoryContract $factory;
    private UserModel $model;

    public function __construct(
        UserFactoryContract $factory,
    ) {
        $this->factory = $factory;
    }

    /**
     * @param UserModel $model
     * @return $this
     */
    public function setModel($model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function toDomain(): User
    {
        $user = $this->factory->buildUser(
            $this->factory->buildId($this->model->id()),
            $this->factory->buildEmployeeId($this->model->employeeId()),
            $this->factory->buildProfileId($this->model->profileId()),
            $this->factory->buildLogin($this->model->login()),
            $this->factory->buildPassword($this->model->password()),
            $this->factory->buildState($this->model->state()),
            $this->factory->buildCreatedAt($this->model->createdAt())
        );

        $user->setPhoto($this->factory->buildUserPhoto($this->model->photo()));
        $user->setUpdatedAt($this->factory->buildUpdatedAt($this->model->updatedAt()));

        return $user;
    }
}
