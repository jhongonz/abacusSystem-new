<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\Contracts;

use Core\SharedContext\Model\ValueObjectStatus;
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

interface UserFactoryContract
{
    /**
     * @param array<string, mixed> $data
     * @return User
     */
    public function buildUserFromArray(array $data): User;

    public function buildUser(
        UserId $id,
        UserEmployeeId $employeeId,
        UserProfileId $profileId,
        UserLogin $login,
        UserPassword $password,
        UserState $state = new UserState,
        UserCreatedAt $createdAt = new UserCreatedAt
    ): User;

    public function buildId(?int $id = null): UserId;

    public function buildEmployeeId(?int $employeeId = null): UserEmployeeId;

    public function buildProfileId(?int $profileId = null): UserProfileId;

    public function buildLogin(string $login): UserLogin;

    public function buildPassword(string $password): UserPassword;

    public function buildState(int $state = ValueObjectStatus::STATE_NEW): UserState;

    public function buildCreatedAt(DateTime $createdAt = new DateTime('now')): UserCreatedAt;

    public function buildUpdatedAt(?DateTime $updatedAt): UserUpdatedAt;

    public function buildUserPhoto(?string $photo = null): UserPhoto;
}
