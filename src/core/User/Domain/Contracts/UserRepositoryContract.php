<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\Contracts;

use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;

interface UserRepositoryContract
{
    public function find(UserId $id): ?User;

    public function findCriteria(UserLogin $login): ?User;

    public function delete(UserId $id): void;

    public function persistUser(User $user): User;
}
