<?php

namespace Core\Profile\Application\UseCases\DeleteProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Domain\ValueObjects\ProfileId;

class DeleteProfileRequest implements RequestService
{
    public function __construct(
        private readonly ProfileId $id,
    ) {
    }

    public function id(): ProfileId
    {
        return $this->id;
    }
}
