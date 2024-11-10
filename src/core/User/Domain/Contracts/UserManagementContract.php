<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Domain\Contracts;

use Core\User\Domain\User;

interface UserManagementContract
{
    public function searchUserById(?int $id): ?User;

    public function searchUserByLogin(string $login): ?User;

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return User
     */
    public function updateUser(int $id, array $data): User;

    /**
     * @param array<string, mixed> $data
     * @return User
     */
    public function createUser(array $data): User;

    public function deleteUser(int $id): void;
}
