<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\Contracts;

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
    public function buildUserFromArray(array $data): User;

    public function buildUser(
        UserId         $id,
        UserEmployeeId $employeeId,
        UserProfileId $profileId,
        UserLogin      $login,
        UserPassword   $password,
        UserState $state = new UserState(),
        UserCreatedAt $createdAt = new UserCreatedAt()
    ): User;

    public function buildId(null|int $id = null): UserId;

    public function buildEmployeeId(null|int $employeeId = null): UserEmployeeId;

    public function buildProfileId(null|int $profileId = null): UserProfileId;

    public function buildLogin(string $login): UserLogin;

    public function buildPassword(string $password): UserPassword;

    public function buildState(null|int $state = null): UserState;

    public function buildCreatedAt(null|DateTime $createdAt): UserCreatedAt;

    public function buildUpdatedAt(null|DateTime $updatedAt): UserUpdatedAt;

    public function buildUserPhoto(?string $photo = null): UserPhoto;
}
