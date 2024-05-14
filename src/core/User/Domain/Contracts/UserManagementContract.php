<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\Contracts;

use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;

interface UserManagementContract
{
    public function searchUserById(UserId $id): ?User;

    public function searchUserByLogin(UserLogin $login): ?User;

    public function updateUser(UserId $id, array $data): void;

    public function createUser(User $user): void;

    public function deleteUser(UserId $id): void;
}
