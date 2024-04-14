<?php

namespace Core\User\Domain\Contracts;

use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;

interface UserRepositoryContract
{
    public function find(UserId $id): null|User;

    public function findCriteria(UserLogin $login): null|User;

    public function delete(UserId $id): void;

    public function persistUser(User $user): User;
}
