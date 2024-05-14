<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Application\Factory;

use Core\User\Domain\Contracts\UserFactoryContract;
use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserCreatedAt;
use Core\User\Domain\ValueObjects\UserEmployeeId;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Domain\ValueObjects\UserPassword;
use Core\User\Domain\ValueObjects\UserPhoto;
use Core\User\Domain\ValueObjects\UserProfileId;
use Core\User\Domain\ValueObjects\UserState;
use Core\User\Domain\ValueObjects\UserUpdatedAt;
use DateTime;
use Exception;

class UserFactory implements UserFactoryContract
{
    /**
     * @throws Exception
     */
    public function buildUserFromArray(array $data): User
    {
        $data = $data[User::TYPE];
        $user = $this->buildUser(
            $this->buildId($data['id']),
            $this->buildEmployeeId($data['employeeId']),
            $this->buildProfileId($data['profileId']),
            $this->buildLogin($data['login']),
            $this->buildPassword($data['password']),
            $this->buildState($data['state']),
            $this->buildCreatedAt(
                new DateTime($data['createdAt']['date'])
            )
        );
        $user->photo()->setValue($data['photo']);

        return $user;
    }

    public function buildUser(
        UserId $id,
        UserEmployeeId $employeeId,
        UserProfileId $profileId,
        UserLogin $login,
        UserPassword $password,
        UserState $state = new UserState,
        UserCreatedAt $createdAt = new UserCreatedAt
    ): User {
        return new User(
            $id,
            $employeeId,
            $profileId,
            $login,
            $password,
            $state,
            $createdAt
        );
    }

    public function buildId(?int $id = null): UserId
    {
        return new UserId($id);
    }

    public function buildEmployeeId(?int $employeeId = null): UserEmployeeId
    {
        return new UserEmployeeId($employeeId);
    }

    public function buildLogin(string $login): UserLogin
    {
        return new UserLogin($login);
    }

    public function buildPassword(string $password): UserPassword
    {
        return new UserPassword($password);
    }

    /**
     * @throws Exception
     */
    public function buildState(?int $state = null): UserState
    {
        return new UserState($state);
    }

    public function buildCreatedAt(?DateTime $createdAt): UserCreatedAt
    {
        return new UserCreatedAt($createdAt);
    }

    public function buildUpdatedAt(?DateTime $updatedAt = null): UserUpdatedAt
    {
        return new UserUpdatedAt($updatedAt);
    }

    public function buildProfileId(?int $profileId = null): UserProfileId
    {
        return new UserProfileId($profileId);
    }

    public function buildUserPhoto(?string $photo = null): UserPhoto
    {
        return new UserPhoto($photo);
    }
}
