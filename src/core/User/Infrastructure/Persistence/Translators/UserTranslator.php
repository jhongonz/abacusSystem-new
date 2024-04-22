<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Infrastructure\Persistence\Translators;

use App\Models\User as UserModel;
use Core\SharedContext\Infrastructure\Translators\TranslatorDomainContract;
use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\User;
use DateTime;
use Exception;

class UserTranslator implements TranslatorDomainContract
{
    private UserFactoryContract $factory;
    private UserModel $user;

    public function __construct(
        UserFactoryContract $factory,
        UserModel $user,
    ) {
        $this->factory = $factory;
        $this->user = $user;
    }

    /**
     * @param UserModel $model
     * @return $this
     */
    public function setModel($model): self
    {
        $this->user = $model;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function toDomain(): User
    {
        $user = $this->factory->buildUser(
            $this->factory->buildId($this->user->id()),
            $this->factory->buildEmployeeId($this->user->employeeId()),
            $this->factory->buildProfileId($this->user->profileId()),
            $this->factory->buildLogin($this->user->login()),
            $this->factory->buildPassword($this->user->password()),
            $this->factory->buildState($this->user->state()),
            $this->factory->buildCreatedAt(
                new DateTime($this->user->createAt())
            )
        );

        $user->setPhoto($this->factory->buildUserPhoto($this->user->photo()));
        $user->setUpdatedAt($this->factory->buildUpdatedAt(
            new DateTime($this->user->updatedAt()))
        );

        return $user;
    }

    public function canTranslate(): string
    {
        return UserModel::class;
    }
}
