<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Application\Factory;

use Core\SharedContext\Model\ValueObjectStatus;
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
     * @param array<string, mixed> $data
     * @return User
     * @throws Exception
     */
    public function buildUserFromArray(array $data): User
    {
        /** @var array{
         *     id: int,
         *     employeeId: int,
         *     profileId: int,
         *     login: string,
         *     password: string,
         *     state: int,
         *     createdAt: string|null,
         *     updatedAt: string|null,
         *     photo: string|null
         * } $dataUser
         */
        $dataUser = $data[User::TYPE];

        $user = $this->buildUser(
            $this->buildId($dataUser['id']),
            $this->buildEmployeeId($dataUser['employeeId']),
            $this->buildProfileId($dataUser['profileId']),
            $this->buildLogin($dataUser['login']),
            $this->buildPassword($dataUser['password']),
            $this->buildState($dataUser['state'])
        );

        if (isset($dataUser['createdAt'])) {
            $user->createdAt()->setValue($this->getDateTime($dataUser['createdAt']));
        }

        if (isset($dataUser['updatedAt'])) {
            $user->updatedAt()->setValue($this->getDateTime($dataUser['updatedAt']));
        }

        if (isset($dataUser['photo'])) {
            $user->photo()->setValue($dataUser['photo']);
        }

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
    public function buildState(int $state = ValueObjectStatus::STATE_NEW): UserState
    {
        return new UserState($state);
    }

    public function buildCreatedAt(DateTime $createdAt = new DateTime('now')): UserCreatedAt
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

    /**
     * @throws Exception
     */
    private function getDateTime(string $dateTime): DateTime
    {
        return new DateTime($dateTime);
    }
}
