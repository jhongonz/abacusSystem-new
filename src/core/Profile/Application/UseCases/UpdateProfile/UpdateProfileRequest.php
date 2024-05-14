<?php

namespace Core\Profile\Application\UseCases\UpdateProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Domain\ValueObjects\ProfileId;

class UpdateProfileRequest implements RequestService
{
    private ProfileId $profileId;

    private array $data;

    public function __construct(
        ProfileId $id,
        array $data,
    ) {
        $this->profileId = $id;
        $this->data = $data;
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
