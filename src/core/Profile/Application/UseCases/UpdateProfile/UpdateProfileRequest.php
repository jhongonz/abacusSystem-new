<?php

namespace Core\Profile\Application\UseCases\UpdateProfile;

use Core\Profile\Application\UseCases\RequestService;
use Core\Profile\Domain\ValueObjects\ProfileId;

class UpdateProfileRequest implements RequestService
{
    /**
     * @param ProfileId $profileId
     * @param array<string, mixed> $data
     */
    public function __construct(
        private readonly ProfileId $profileId,
        private readonly array $data,
    ) {
    }

    public function profileId(): ProfileId
    {
        return $this->profileId;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return $this->data;
    }
}
