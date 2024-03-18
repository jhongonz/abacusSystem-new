<?php

namespace Core\User\Application\UseCases\SearchUser;

use Core\User\Application\UseCases\RequestService;
use Core\User\Domain\ValueObjects\UserLogin;

class SearchUserByLoginRequest implements RequestService
{
    private UserLogin $login;
    
    public function __construct(UserLogin $login)
    {
        $this->login = $login;
    }
    
    public function login(): UserLogin
    {
        return $this->login;
    }
}