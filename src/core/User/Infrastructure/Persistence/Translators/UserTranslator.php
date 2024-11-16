<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Infrastructure\Persistence\Translators;

use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\User;
use Core\User\Infrastructure\Persistence\Eloquent\Model\User as UserModel;
use Exception;

class UserTranslator
{
    private UserModel $model;

    public function __construct(
        private readonly UserFactoryContract $factory,
    ) {
    }

    public function setModel(UserModel $model): self
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
            $this->factory->buildLogin($this->model->login() ?? ''),
            $this->factory->buildPassword($this->model->password() ?? ''),
            $this->factory->buildState($this->model->state())
        );

        $user->setPhoto($this->factory->buildUserPhoto($this->model->photo()));

        if (! is_null($this->model->createdAt())) {
            $user->setCreatedAt(
                $this->factory->buildCreatedAt($this->model->createdAt())
            );
        }

        if (! is_null($this->model->updatedAt())) {
            $user->setUpdatedAt(
                $this->factory->buildUpdatedAt($this->model->updatedAt())
            );
        }

        return $user;
    }
}
