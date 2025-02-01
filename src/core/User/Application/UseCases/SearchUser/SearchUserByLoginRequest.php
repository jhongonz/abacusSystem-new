<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Application\UseCases\SearchUser;

use Core\User\Application\UseCases\RequestService;
use Core\User\Domain\ValueObjects\UserLogin;

class SearchUserByLoginRequest implements RequestService
{
    public function __construct(
        private readonly UserLogin $login,
    ) {
    }

    public function login(): UserLogin
    {
        return $this->login;
    }
}
