<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain;

use Core\User\Domain\ValueObjects\UserCreatedAt;
use Core\User\Domain\ValueObjects\UserEmployeeId;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;
use Core\User\Domain\ValueObjects\UserPassword;
use Core\User\Domain\ValueObjects\UserPhoto;
use Core\User\Domain\ValueObjects\UserProfileId;
use Core\User\Domain\ValueObjects\UserState;
use Core\User\Domain\ValueObjects\UserUpdatedAt;

class User
{
    public const TYPE = 'user';

    private UserId $id;

    private UserEmployeeId $employeeId;

    private UserProfileId $profileId;

    private UserLogin $login;

    private UserPassword $password;

    private UserState $state;

    private UserCreatedAt $createdAt;

    private UserUpdatedAt $updatedAt;

    private UserPhoto $photo;

    public function __construct(
        UserId $id,
        UserEmployeeId $employeeId,
        UserProfileId $profileId,
        UserLogin $login,
        UserPassword $password,
        UserState $state = new UserState,
        UserCreatedAt $createdAt = new UserCreatedAt,
    ) {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->profileId = $profileId;
        $this->login = $login;
        $this->password = $password;

        $this->state = $state;
        $this->createdAt = $createdAt;

        $this->updatedAt = new UserUpdatedAt;
        $this->photo = new UserPhoto;
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function setId(UserId $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function employeeId(): UserEmployeeId
    {
        return $this->employeeId;
    }

    public function setEmployeeId(UserEmployeeId $employeeId): self
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    public function profileId(): UserProfileId
    {
        return $this->profileId;
    }

    public function setProfileId(UserProfileId $profileId): self
    {
        $this->profileId = $profileId;

        return $this;
    }

    public function login(): UserLogin
    {
        return $this->login;
    }

    public function setLogin(UserLogin $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function password(): UserPassword
    {
        return $this->password;
    }

    public function setPassword(UserPassword $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function state(): UserState
    {
        return $this->state;
    }

    public function setState(UserState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function createdAt(): UserCreatedAt
    {
        return $this->createdAt;
    }

    public function setCreatedAt(UserCreatedAt $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function updatedAt(): UserUpdatedAt
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(UserUpdatedAt $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function photo(): UserPhoto
    {
        return $this->photo;
    }

    public function setPhoto(UserPhoto $photo): self
    {
        $this->photo = $photo;

        return $this;
    }
}
