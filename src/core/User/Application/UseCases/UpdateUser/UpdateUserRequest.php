<?php

namespace Core\User\Application\UseCases\UpdateUser;

use Core\User\Application\UseCases\RequestService;
use Core\User\Domain\ValueObjects\UserId;

class UpdateUserRequest implements RequestService
{
    private UserId $id;
    private array $data;
    
    public function __construct(
      UserId $id,
      array $data,
    ) {
        $this->id = $id;
        $this->data = $data;
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