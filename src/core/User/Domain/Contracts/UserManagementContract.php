<?php

namespace Core\User\Domain\Contracts;

use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;

interface UserManagementContract
{
    public function searchUserById(UserId $id): null|User;
    public function searchUserByLogin(UserLogin $login): null|User;
    public function updateUser(UserId $id, array $data): void;
    public function createUser(User $user): void;
}
