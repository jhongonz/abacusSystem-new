<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Application\UseCases\SearchUser;

use Core\User\Application\UseCases\RequestService;
use Core\User\Domain\ValueObjects\UserId;

class SearchUserByIdRequest implements RequestService
{
    public function __construct(
        private readonly UserId $id,
    ) {
    }

    public function userId(): UserId
    {
        return $this->id;
    }
}
