<?php

namespace Core\Profile\Application\UseCases\UpdateProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Domain\ValueObjects\ProfileId;

class UpdateProfileRequest implements RequestService
{
    public function __construct(
        private readonly ProfileId $profileId,
        private readonly array $data,
    ) {
    }

    public function profileId(): ProfileId
    {
        return $this->profileId;
    }

    public function data(): array
    {
        return $this->data;
    }
}
