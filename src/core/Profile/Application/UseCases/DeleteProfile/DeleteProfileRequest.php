<?php

namespace Core\Profile\Application\UseCases\DeleteProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Domain\ValueObjects\ProfileId;

class DeleteProfileRequest implements RequestService
{
    private ProfileId $id;

    public function __construct(ProfileId $id)
    {
        $this->id = $id;
    }

    public function id(): ProfileId
    {
        return $this->id;
    }
}
