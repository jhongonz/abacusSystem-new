<?php

namespace Core\User\Domain\Contracts;

use Core\User\Domain\User;
use Core\User\Domain\ValueObjects\UserId;
use Core\User\Domain\ValueObjects\UserLogin;

interface UserRepositoryContract
{
    public function find(UserId $id): null|User;
    
    public function findCriteria(UserLogin $login): null|User;
    
    public function save(User $user): void;
    
    public function update(UserId $id, User $user): void;
    
    public function delete(UserId $id): void;
    
    public function persistUser(User $user): User;
}