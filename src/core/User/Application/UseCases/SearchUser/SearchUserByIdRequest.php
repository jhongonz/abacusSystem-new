<?php

namespace Core\User\Application\UseCases\SearchUser;

use Core\User\Application\UseCases\RequestService;
use Core\User\Domain\ValueObjects\UserId;

class SearchUserByIdRequest implements RequestService
{
    private UserId $id;

    public function __construct(UserId $id)
    {
        $this->id = $id;
    }

    public function userId(): UserId
    {
        return $this->id;
    }
}