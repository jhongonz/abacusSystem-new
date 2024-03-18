<?php

namespace Core\User\Domain\Contracts;

use Core\User\Domain\User;

interface UserDataTransformerContract
{
    public function write(User $user): UserDataTransformerContract;
    
    public function read(): array;
}