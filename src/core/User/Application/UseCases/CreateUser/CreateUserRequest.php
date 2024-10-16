<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Application\UseCases\CreateUser;

use Core\User\Application\UseCases\RequestService;
use Core\User\Domain\User;

class CreateUserRequest implements RequestService
{
    public function __construct(
        private readonly User $user
    ) {
    }

    public function user(): User
    {
        return $this->user;
    }
}
