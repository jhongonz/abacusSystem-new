<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\Contracts;

use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;

interface UserManagementContract
{
    public function searchUserById(?int $id): ?User;

    public function searchUserByLogin(string $login): ?User;

    public function updateUser(int $id, array $data): void;

    public function createUser(array $data): User;

    public function deleteUser(int $id): void;
}
