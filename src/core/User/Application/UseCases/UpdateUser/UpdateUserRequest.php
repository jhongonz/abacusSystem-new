<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 */

namespace Core\User\Application\UseCases\UpdateUser;

use Core\User\Application\UseCases\RequestService;
use Core\User\Domain\ValueObjects\UserId;

class UpdateUserRequest implements RequestService
{
    public function __construct(
        private readonly UserId $id,
        private readonly array $data,
    ) {
    }

    public function userId(): UserId
    {
        return $this->id;
    }

    public function data(): array
    {
        return $this->data;
    }
}
